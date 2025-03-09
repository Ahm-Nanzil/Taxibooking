<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStop;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()->latest()->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        return view('bookings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'pickup_time' => 'required|date|after:now',
            'passengers' => 'required|integer|min:1|max:4',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking = new Booking();
        $booking->user_id = Auth::id();
        $booking->pickup_location = $request->pickup_location;
        $booking->dropoff_location = $request->dropoff_location;
        $booking->pickup_lat = $request->pickup_lat;
        $booking->pickup_lng = $request->pickup_lng;
        $booking->dropoff_lat = $request->dropoff_lat;
        $booking->dropoff_lng = $request->dropoff_lng;
        $booking->pickup_time = $request->pickup_time;
        $booking->passengers = $request->passengers;
        $booking->notes = $request->notes;
        $booking->status = 'pending';
        $booking->save();

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully! We will assign a driver shortly.');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return view('bookings.show', compact('booking'));
    }

    public function accept(Booking $booking, Request $request)
    {
        $this->authorize('manage', $booking);

        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id'
        ]);

        $booking->update([
            'driver_id' => $validated['driver_id'],
            'status' => 'accepted'
        ]);

        // Notify customer about booking acceptance
        // event(new BookingAccepted($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking accepted and driver assigned successfully.');
    }

    public function reject(Booking $booking, Request $request)
    {
        $this->authorize('manage', $booking);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string'
        ]);

        $booking->update([
            'status' => 'rejected',
            'cancellation_reason' => $validated['cancellation_reason']
        ]);

        // Process refund if payment was made
        if ($booking->payment && $booking->payment->status === 'completed') {
            $booking->payment->update(['status' => 'refunded']);
            // Process actual refund through payment gateway
        }

        // Notify customer about booking rejection
        // event(new BookingRejected($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking rejected successfully.');
    }

    public function complete(Booking $booking)
    {
        $this->authorize('manage', $booking);

        $booking->update(['status' => 'completed']);

        // Update driver status to available
        $booking->driver->update(['status' => 'available']);

        // Notify customer about ride completion
        // event(new BookingCompleted($booking));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Ride completed successfully.');
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return back()->with('success', 'Booking cancelled successfully.');
    }

    private function calculateFare($pickupLat, $pickupLng, $dropoffLat, $dropoffLng, $stops = [])
    {
        // Basic distance calculation using Haversine formula
        $baseDistance = $this->calculateDistance($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);

        // Add distance for stops if any
        $stopDistance = 0;
        $lastLat = $pickupLat;
        $lastLng = $pickupLng;

        foreach ($stops as $stop) {
            $stopDistance += $this->calculateDistance($lastLat, $lastLng, $stop['lat'], $stop['lng']);
            $lastLat = $stop['lat'];
            $lastLng = $stop['lng'];
        }

        if (!empty($stops)) {
            $stopDistance += $this->calculateDistance($lastLat, $lastLng, $dropoffLat, $dropoffLng);
        }

        $totalDistance = $baseDistance + $stopDistance;

        // Calculate fare based on distance
        $baseFare = 5.00; // Base fare in your currency
        $perKmRate = 2.50; // Rate per kilometer

        return $baseFare + ($totalDistance * $perKmRate);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in km
    }
}
