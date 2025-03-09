<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RideXpress</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="relative min-h-screen bg-gray-100">
        @if (Route::has('login'))
            <div class="p-6 text-right">
                @auth
                    <a href="{{ route('bookings.index') }}" class="font-semibold text-gray-600 hover:text-gray-900">My Bookings</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                    Welcome to RideXpress
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Book your ride with ease. Safe, reliable, and convenient taxi service at your fingertips.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    @auth
                        <a href="{{ route('bookings.create') }}" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Book Now
                        </a>
                        <a href="{{ route('bookings.index') }}" class="text-sm font-semibold leading-6 text-gray-900">
                            View My Bookings <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Get Started
                        </a>
                        <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900">
                            Login <span aria-hidden="true">→</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-24">
                <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="text-center">
                        <div class="flex justify-center">
                            <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold">Quick Booking</h3>
                        <p class="mt-2 text-gray-600">Book your ride in seconds with our easy-to-use platform</p>
                    </div>

                    <div class="text-center">
                        <div class="flex justify-center">
                            <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold">Safe & Reliable</h3>
                        <p class="mt-2 text-gray-600">All our drivers are verified and trained professionals</p>
                    </div>

                    <div class="text-center">
                        <div class="flex justify-center">
                            <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold">Multiple Payment Options</h3>
                        <p class="mt-2 text-gray-600">Pay with card or cash, whatever suits you best</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
