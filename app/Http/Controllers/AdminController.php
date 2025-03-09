<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function dashboard(): View
    {
        $totalUsers = User::where('is_admin', false)->count();
        $totalDrivers = User::where('is_driver', true)->count();
        $totalBookings = Booking::count();
        $recentBookings = Booking::with(['user', 'driver'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDrivers',
            'totalBookings',
            'recentBookings'
        ));
    }

    public function users(): View
    {
        $users = User::where('is_admin', false)
            ->where('is_driver', false)
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function drivers(): View
    {
        $drivers = User::where('is_driver', true)
            ->withCount(['completedBookings'])
            ->latest()
            ->paginate(10);

        return view('admin.drivers.index', compact('drivers'));
    }

    public function bookings(): View
    {
        $bookings = Booking::with(['user', 'driver'])
            ->latest()
            ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }
}
