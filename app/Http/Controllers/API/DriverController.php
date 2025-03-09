<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DriverController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');

        $drivers = User::where('is_driver', true)
            ->withCount(['completedBookings' => function($query) {
                $query->where('status', 'completed');
            }])
            ->get();

        return response()->json(['drivers' => $drivers]);
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:users,license_number',
            'vehicle_number' => 'required|string',
            'vehicle_model' => 'required|string',
        ]);

        $driver = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt(str_random(10)), // temporary password
            'is_driver' => true,
            'license_number' => $validated['license_number'],
            'vehicle_number' => $validated['vehicle_number'],
            'vehicle_model' => $validated['vehicle_model'],
        ]);

        return response()->json([
            'message' => 'Driver created successfully',
            'driver' => $driver
        ], 201);
    }

    public function show(User $driver)
    {
        Gate::authorize('admin');

        if (!$driver->is_driver) {
            return response()->json([
                'message' => 'User is not a driver'
            ], 404);
        }

        return response()->json([
            'driver' => $driver->load(['completedBookings' => function($query) {
                $query->where('status', 'completed');
            }])
        ]);
    }

    public function update(Request $request, User $driver)
    {
        Gate::authorize('admin');

        if (!$driver->is_driver) {
            return response()->json([
                'message' => 'User is not a driver'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:users,license_number,' . $driver->id,
            'vehicle_number' => 'required|string',
            'vehicle_model' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $driver->update($validated);

        return response()->json([
            'message' => 'Driver updated successfully',
            'driver' => $driver
        ]);
    }

    public function destroy(User $driver)
    {
        Gate::authorize('admin');

        if (!$driver->is_driver) {
            return response()->json([
                'message' => 'User is not a driver'
            ], 404);
        }

        $driver->delete();

        return response()->json([
            'message' => 'Driver deleted successfully'
        ]);
    }

    public function currentBookings(User $driver)
    {
        Gate::authorize('admin');

        if (!$driver->is_driver) {
            return response()->json([
                'message' => 'User is not a driver'
            ], 404);
        }

        $bookings = $driver->assignedBookings()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with('user')
            ->get();

        return response()->json([
            'bookings' => $bookings
        ]);
    }
}
