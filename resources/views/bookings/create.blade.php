<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book a Ride') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Map Section -->
                            <div class="md:col-span-2">
                                <div id="map" class="w-full h-96 rounded-lg mb-4"></div>
                            </div>

                            <!-- Pickup Location -->
                            <div>
                                <x-input-label for="pickup_location" :value="__('Pickup Location')" />
                                <div class="mt-1 relative">
                                    <x-text-input id="pickup_location" class="block mt-1 w-full"
                                        type="text"
                                        name="pickup_location"
                                        required />
                                    <input type="hidden" name="pickup_lat" id="pickup_lat">
                                    <input type="hidden" name="pickup_lng" id="pickup_lng">
                                </div>
                                <x-input-error :messages="$errors->get('pickup_location')" class="mt-2" />
                            </div>

                            <!-- Dropoff Location -->
                            <div>
                                <x-input-label for="dropoff_location" :value="__('Dropoff Location')" />
                                <div class="mt-1 relative">
                                    <x-text-input id="dropoff_location" class="block mt-1 w-full"
                                        type="text"
                                        name="dropoff_location"
                                        required />
                                    <input type="hidden" name="dropoff_lat" id="dropoff_lat">
                                    <input type="hidden" name="dropoff_lng" id="dropoff_lng">
                                </div>
                                <x-input-error :messages="$errors->get('dropoff_location')" class="mt-2" />
                            </div>

                            <!-- Pickup Time -->
                            <div>
                                <x-input-label for="pickup_time" :value="__('Pickup Time')" />
                                <x-text-input id="pickup_time" class="block mt-1 w-full"
                                    type="datetime-local"
                                    name="pickup_time"
                                    required />
                                <x-input-error :messages="$errors->get('pickup_time')" class="mt-2" />
                            </div>

                            <!-- Number of Passengers -->
                            <div>
                                <x-input-label for="passengers" :value="__('Number of Passengers')" />
                                <x-text-input id="passengers" class="block mt-1 w-full"
                                    type="number"
                                    name="passengers"
                                    min="1"
                                    max="4"
                                    value="1"
                                    required />
                                <x-input-error :messages="$errors->get('passengers')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Additional Notes')" />
                                <textarea id="notes" name="notes"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="3"></textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <!-- Estimated Fare -->
                            <div class="md:col-span-2">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-gray-600">Estimated Fare</div>
                                    <div class="text-2xl font-semibold" id="estimated_fare">₹ 0.00</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <x-primary-button>
                                {{ __('Book Now') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let map;
        let pickupMarker;
        let dropoffMarker;
        let directionsService;
        let directionsRenderer;
        const baseRate = 50; // Base fare in rupees
        const perKmRate = 12; // Rate per kilometer

        function initMap() {
            // Initialize the map centered on a default location (e.g., city center)
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 20.5937, lng: 78.9629 }, // India's center coordinates
                zoom: 12
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true
            });

            // Initialize autocomplete for pickup location
            const pickupInput = document.getElementById('pickup_location');
            const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput);
            pickupAutocomplete.addListener('place_changed', () => {
                const place = pickupAutocomplete.getPlace();
                if (place.geometry) {
                    if (pickupMarker) pickupMarker.setMap(null);
                    pickupMarker = new google.maps.Marker({
                        position: place.geometry.location,
                        map: map,
                        title: 'Pickup Location',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                        }
                    });
                    document.getElementById('pickup_lat').value = place.geometry.location.lat();
                    document.getElementById('pickup_lng').value = place.geometry.location.lng();
                    updateRoute();
                }
            });

            // Initialize autocomplete for dropoff location
            const dropoffInput = document.getElementById('dropoff_location');
            const dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffInput);
            dropoffAutocomplete.addListener('place_changed', () => {
                const place = dropoffAutocomplete.getPlace();
                if (place.geometry) {
                    if (dropoffMarker) dropoffMarker.setMap(null);
                    dropoffMarker = new google.maps.Marker({
                        position: place.geometry.location,
                        map: map,
                        title: 'Dropoff Location',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        }
                    });
                    document.getElementById('dropoff_lat').value = place.geometry.location.lat();
                    document.getElementById('dropoff_lng').value = place.geometry.location.lng();
                    updateRoute();
                }
            });

            // Try to get user's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        // Optionally set this as pickup location
                        reverseGeocode(pos, pickupInput);
                    },
                    () => {
                        console.log('Error: The Geolocation service failed.');
                    }
                );
            }
        }

        function reverseGeocode(latLng, input) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK') {
                    if (results[0]) {
                        input.value = results[0].formatted_address;
                        document.getElementById('pickup_lat').value = latLng.lat;
                        document.getElementById('pickup_lng').value = latLng.lng;
                        if (pickupMarker) pickupMarker.setMap(null);
                        pickupMarker = new google.maps.Marker({
                            position: latLng,
                            map: map,
                            title: 'Pickup Location',
                            icon: {
                                url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                            }
                        });
                    }
                }
            });
        }

        function updateRoute() {
            if (!pickupMarker || !dropoffMarker) return;

            const request = {
                origin: pickupMarker.getPosition(),
                destination: dropoffMarker.getPosition(),
                travelMode: 'DRIVING'
            };

            directionsService.route(request, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    // Calculate and display estimated fare
                    const distance = result.routes[0].legs[0].distance.value / 1000; // Convert meters to kilometers
                    const estimatedFare = calculateFare(distance);
                    document.getElementById('estimated_fare').textContent = `₹ ${estimatedFare.toFixed(2)}`;
                }
            });
        }

        function calculateFare(distance) {
            return baseRate + (distance * perKmRate);
        }

        // Set minimum datetime for pickup_time to current time
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            now.setMinutes(now.getMinutes() + 30); // Add 30 minutes to current time
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('pickup_time').min = minDateTime;
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
    @endpush
</x-app-layout>
