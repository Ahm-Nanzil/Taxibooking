<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->is_admin
            ? Booking::with(['user', 'driver'])->latest()->get()
            : Auth::user()->bookings()->with(['driver'])->latest()->get();

        return response()->json(['bookings' => $bookings]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_location' => 'required|string',
            'dropoff_location' => 'required|string',
            'pickup_time' => 'required|date',
            'passengers' => 'required|integer|min:1|max:' . config('taxi.booking.max_passengers', 4),
            'notes' => 'nullable|string'
        ]);

        $booking = Auth::user()->bookings()->create([
            'pickup_location' => $validated['pickup_location'],
            'dropoff_location' => $validated['dropoff_location'],
            'pickup_time' => $validated['pickup_time'],
            'passengers' => $validated['passengers'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'estimated_fare' => $this->calculateEstimatedFare($validated['pickup_location'], $validated['dropoff_location'])
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking->load('user')
        ], 201);
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return response()->json(['booking' => $booking->load(['user', 'driver'])]);
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorize('manage', $booking);

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,completed,cancelled',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $booking->fresh(['user', 'driver'])
        ]);
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending bookings can be cancelled'
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking
        ]);
    }

    private function calculateEstimatedFare($pickup, $dropoff)
    {
        // In a real application, this would use a mapping service to calculate distance
        // For now, we'll return a dummy value based on config
        $baseFare = config('taxi.fare.base_fare', 10);
        $perKmRate = config('taxi.fare.per_km_rate', 2);
        $estimatedKm = 5; // Dummy value, should be calculated using Google Maps API

        return $baseFare + ($perKmRate * $estimatedKm);
    }
}
