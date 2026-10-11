{{-- Form bersama untuk create/edit perangkat (FR-002).
    $device: model Device — kosong saat create. --}}
<form method="POST" action="{{ $device->exists ? route('devices.update', $device) : route('devices.store') }}"
      class="mt-6 grid max-w-xl gap-6">
    @csrf
    @if ($device->exists)
        @method('PUT')
    @endif

    <flux:input label="Nama perangkat" name="name" required
                :value="old('name', $device->name)" />

    <flux:input label="Host / IP" name="host" required
                :value="old('host', $device->host)" />

    <flux:input label="Port API-SSL" name="port" type="number" min="1" max="65535" required
                :value="old('port', $device->port ?? 8729)" />

    <flux:input label="Username API (akun read-only di router)" name="api_username"
                :value="old('api_username', $device->api_username)" />

    @if ($device->exists)
        <flux:input label="Password API" name="api_password" type="password"
                    placeholder="Biarkan kosong = tidak diubah" />
    @else
        <flux:input label="Password API" name="api_password" type="password" />
    @endif

    <flux:checkbox label="Aktifkan monitoring" name="monitoring_enabled"
                   :checked="old('monitoring_enabled', $device->monitoring_enabled ?? false)" />

    <flux:textarea label="Metadata (JSON, opsional)" name="metadata" rows="3"
                   :value="old('metadata', $device->metadata ? json_encode($device->metadata) : null)" />

    <div class="flex gap-3">
        <flux:button variant="primary" type="submit">
            {{ $device->exists ? 'Simpan' : 'Tambah' }}
        </flux:button>
        <flux:button variant="ghost" href="{{ route('devices.index') }}">Batal</flux:button>
    </div>
</form>
