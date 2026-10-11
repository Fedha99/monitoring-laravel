<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeviceController extends Controller
{
    /**
     * FR-002: admin mengelola perangkat; operator hanya melihat (DevicePolicy).
     * FR-011: pencatatan audit perubahan perangkat disiapkan pada M4.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Device::class);

        return view('devices.index', [
            'devices' => Device::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Device::class);

        return view('devices.create', ['device' => new Device]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Device::class);

        $validated = $this->validated($request);

        $device = Device::create($validated);

        return redirect()
            ->route('devices.index')
            ->with('status', "Perangkat {$device->name} ditambahkan.");
    }

    public function edit(Device $device): View
    {
        Gate::authorize('update', $device);

        return view('devices.edit', ['device' => $device]);
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        Gate::authorize('update', $device);

        $validated = $this->validated($request, $device);

        // NFR-002: api_password write-only — kosong berarti tidak diubah.
        if (blank($validated['api_password'] ?? null)) {
            unset($validated['api_password']);
        }

        $device->update($validated);

        return redirect()
            ->route('devices.index')
            ->with('status', "Perangkat {$device->name} diperbarui.");
    }

    public function destroy(Device $device): RedirectResponse
    {
        Gate::authorize('delete', $device);

        $name = $device->name;
        $device->delete();

        return redirect()->route('devices.index')->with('status', "Perangkat {$name} dihapus.");
    }

    /**
     * FR-002/FR-003: validasi sisi server saat penyimpanan. Kebijakan
     * allowlist/CIDR penuh ditegakkan oleh service koneksi pada M2,
     * sebelum koneksi dibuat — bukan hanya di form.
     */
    private function validated(Request $request, ?Device $device = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'host' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('devices')->where(
                    fn ($query) => $query
                        ->where('host', $request->input('host'))
                        ->where('port', (int) $request->input('port', 8729))
                )->ignore($device?->id),
            ],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'api_username' => ['nullable', 'string', 'max:255'],
            'api_password' => ['nullable', 'string', 'max:1024'],
            'metadata' => ['nullable', 'string', 'json'],
        ]);

        // Checkbox yang tidak dicentang tidak terkirim — normalisasi ke boolean.
        $validated['monitoring_enabled'] = $request->boolean('monitoring_enabled');

        return $validated;
    }
}
