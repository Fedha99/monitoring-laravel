<x-layouts.app>
    <flux:main>
        <div class="mb-6 flex items-center justify-between">
            <flux:heading level="1" size="text-2xl">Perangkat</flux:heading>

            @can('create', App\Models\Device::class)
                <flux:button variant="primary" href="{{ route('devices.create') }}">Tambah Perangkat</flux:button>
            @endcan
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-50 text-zinc-500 dark:bg-zinc-900 dark:text-zinc-400">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Host:Port</th>
                        <th class="px-4 py-3 font-medium">User API</th>
                        <th class="px-4 py-3 font-medium">Monitoring</th>
                        <th class="px-4 py-3 font-medium">Diubah</th>
                        @can('create', App\Models\Device::class)
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($devices as $device)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $device->name }}</td>
                            <td class="px-4 py-3 font-mono">{{ $device->host }}:{{ $device->port }}</td>
                            <td class="px-4 py-3">{{ $device->api_username ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($device->monitoring_enabled)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200">Aktif</span>
                                @else
                                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-500">{{ $device->updated_at->format('d M Y H:i') }}</td>
                            @can('update', $device)
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:button size="xs" href="{{ route('devices.edit', $device) }}">Edit</flux:button>
                                        <form method="POST" action="{{ route('devices.destroy', $device) }}"
                                              onsubmit="return confirm('Hapus perangkat {{ $device->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button size="xs" variant="danger" type="submit">Hapus</flux:button>
                                        </form>
                                    </div>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-zinc-500">
                                Belum ada perangkat terdaftar.
                                @can('create', App\Models\Device::class)
                                    <a href="{{ route('devices.create') }}" class="text-blue-600 dark:text-blue-400">Tambah perangkat pertama</a>.
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:main>
</x-layouts.app>
