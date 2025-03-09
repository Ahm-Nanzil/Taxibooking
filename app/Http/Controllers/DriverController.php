<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct()
    {
        // $this->middleware('admin');
    }

    public function index()
    {
        $drivers = Driver::withCount('bookings')
            ->orderBy('status')
            ->latest()
            ->paginate(10);

        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'license_number' => 'required|string|unique:drivers',
            'vehicle_number' => 'required|string|unique:drivers'
        ]);

        $driver = Driver::create($validated + ['status' => 'available']);

        return redirect()->route('drivers.show', $driver)
            ->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver)
    {
        $driver->load(['bookings' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'license_number' => 'required|string|unique:drivers,license_number,' . $driver->id,
            'vehicle_number' => 'required|string|unique:drivers,vehicle_number,' . $driver->id,
            'status' => 'required|in:available,busy,offline'
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.show', $driver)
            ->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->bookings()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Cannot delete driver with pending bookings.');
        }

        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }

    public function updateStatus(Driver $driver, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,busy,offline'
        ]);

        $driver->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Driver status updated successfully.');
    }

    public function getAvailable()
    {
        $drivers = Driver::where('status', 'available')->get();
        return response()->json($drivers);
    }
}
