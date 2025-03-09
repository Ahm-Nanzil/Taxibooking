<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Booking Details') }} #{{ $booking->id }}
            </h2>
            <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Back to Bookings') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Status Badge -->
                    <div class="mb-6">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if($booking->status === 'completed') bg-green-100 text-green-800
                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    <!-- Booking Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                            <dl class="grid grid-cols-1 gap-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->user->phone }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Driver Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Driver Information</h3>
                            @if($booking->driver)
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                                        <dd class="text-sm text-gray-900">{{ $booking->driver->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="text-sm text-gray-900">{{ $booking->driver->phone_number }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Vehicle Number</dt>
                                        <dd class="text-sm text-gray-900">{{ $booking->driver->vehicle_number }}</dd>
                                    </div>
                                </dl>
                            @else
                                <p class="text-sm text-gray-500">Driver not yet assigned</p>
                            @endif
                        </div>

                        <!-- Trip Details -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Trip Details</h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pickup Location</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->pickup_address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Drop-off Location</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->dropoff_address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Scheduled Time</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->scheduled_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Number of Passengers</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->passengers }}</dd>
                                </div>
                            </dl>

                            @if($booking->stops->count() > 0)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Additional Stops</h4>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($booking->stops as $stop)
                                            <li class="text-sm text-gray-600">{{ $stop->address }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Map View -->
                            <div id="map" class="w-full h-64 rounded-lg mt-4"></div>
                        </div>

                        <!-- Payment Information -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                            @if($booking->payment)
                                <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                        <dd class="text-sm text-gray-900">${{ number_format($booking->payment->amount, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($booking->payment->status === 'completed') bg-green-100 text-green-800
                                                @elseif($booking->payment->status === 'refunded') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($booking->payment->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                        <dd class="text-sm text-gray-900">{{ ucfirst($booking->payment->payment_method) }}</dd>
                                    </div>
                                </dl>
                            @else
                                <p class="text-sm text-gray-500">No payment information available</p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex justify-end space-x-3">
                        @if($booking->status === 'pending')
                            @if(auth()->user()->isAdmin())
                                <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 assign-driver" data-booking-id="{{ $booking->id }}">
                                    {{ __('Assign Driver') }}
                                </a>
                                <form action="{{ route('bookings.reject', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('Are you sure you want to reject this booking?')">
                                        {{ __('Reject Booking') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        {{ __('Cancel Booking') }}
                                    </button>
                                </form>
                            @endif
                        @elseif($booking->status === 'accepted' && auth()->user()->isAdmin())
                            <form action="{{ route('bookings.complete', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Complete Ride') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: { lat: {{ $booking->pickup_lat }}, lng: {{ $booking->pickup_lng }} }
            });

            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            const waypoints = [
                @foreach($booking->stops as $stop)
                    {
                        location: new google.maps.LatLng({{ $stop->lat }}, {{ $stop->lng }}),
                        stopover: true
                    },
                @endforeach
            ];

            const request = {
                origin: new google.maps.LatLng({{ $booking->pickup_lat }}, {{ $booking->pickup_lng }}),
                destination: new google.maps.LatLng({{ $booking->dropoff_lat }}, {{ $booking->dropoff_lng }}),
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING
            };

            directionsService.route(request, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                }
            });
        }

        window.addEventListener('load', initMap);
    </script>
    @endpush
</x-app-layout>
