<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Driver') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('drivers.update', $driver) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" type="text" name="name" :value="old('name', $driver->name)" required autofocus class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-4">
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <x-text-input id="phone_number" type="tel" name="phone_number" :value="old('phone_number', $driver->phone_number)" required class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <!-- License Number -->
                        <div class="mb-4">
                            <x-input-label for="license_number" :value="__('License Number')" />
                            <x-text-input id="license_number" type="text" name="license_number" :value="old('license_number', $driver->license_number)" required class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                        </div>

                        <!-- Vehicle Number -->
                        <div class="mb-4">
                            <x-input-label for="vehicle_number" :value="__('Vehicle Number')" />
                            <x-text-input id="vehicle_number" type="text" name="vehicle_number" :value="old('vehicle_number', $driver->vehicle_number)" required class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('vehicle_number')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="available" {{ $driver->status === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="busy" {{ $driver->status === 'busy' ? 'selected' : '' }}>Busy</option>
                                <option value="offline" {{ $driver->status === 'offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('drivers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ml-4">
                                {{ __('Update Driver') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
