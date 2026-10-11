<x-layouts.app>
    <flux:main>
        <flux:heading level="1" size="text-2xl">Edit Perangkat — {{ $device->name }}</flux:heading>

        @include('devices._form', ['device' => $device])
    </flux:main>
</x-layouts.app>
