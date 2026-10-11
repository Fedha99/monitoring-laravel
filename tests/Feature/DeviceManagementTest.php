<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('devices.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_operators_see_the_device_list(): void
    {
        $operator = User::factory()->create();
        $device = Device::factory()->create(['name' => 'Router Kantor']);

        $response = $this->actingAs($operator)->get(route('devices.index'));

        $response->assertOk();
        $response->assertSee('Router Kantor');
        $response->assertSee('Nonaktif');
        // Operator tidak melihat tombol aksi tulis
        $response->assertDontSee('Tambah Perangkat');
    }

    public function test_operators_cannot_open_the_create_form(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)->get(route('devices.create'))->assertForbidden();
    }

    public function test_operators_cannot_store_devices(): void
    {
        $operator = User::factory()->create();

        $this->actingAs($operator)->post(route('devices.store'), [
            'name' => 'Router Baru',
            'host' => '10.10.0.1',
            'port' => '8729',
        ])->assertForbidden();

        $this->assertSame(0, Device::count());
    }

    public function test_operators_cannot_delete_devices(): void
    {
        $operator = User::factory()->create();
        $device = Device::factory()->create();

        $this->actingAs($operator)->delete(route('devices.destroy', $device))->assertForbidden();

        $this->assertModelExists($device);
    }

    public function test_admins_can_create_a_device_and_password_is_encrypted_at_rest(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('devices.store'), [
            'name' => 'Router Lab',
            'host' => '192.168.88.1',
            'port' => '8729',
            'api_username' => 'mcc-readonly',
            'api_password' => 'rahasia-lab',
            'monitoring_enabled' => '1',
        ]);

        $response->assertRedirect(route('devices.index'));

        $device = Device::query()->where('host', '192.168.88.1')->firstOrFail();
        $this->assertTrue($device->monitoring_enabled);
        $this->assertSame('rahasia-lab', $device->api_password);
        $this->assertNotSame('rahasia-lab', $device->getRawOriginal('api_password'));
    }

    public function test_duplicate_host_and_port_combination_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Device::factory()->create(['host' => '10.0.0.1', 'port' => 8729]);

        $this->actingAs($admin)->post(route('devices.store'), [
            'name' => 'Duplikat',
            'host' => '10.0.0.1',
            'port' => '8729',
        ])->assertSessionHasErrors('host');
    }

    public function test_invalid_port_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('devices.store'), [
            'name' => 'Port Tidak Valid',
            'host' => '10.0.0.2',
            'port' => '99999',
        ])->assertSessionHasErrors('port');
    }

    public function test_blank_password_keeps_the_existing_credential(): void
    {
        $admin = User::factory()->admin()->create();
        $device = Device::factory()->create(['api_password' => 'password-lama']);

        $this->actingAs($admin)->put(route('devices.update', $device), [
            'name' => 'Router Lab (revisi)',
            'host' => $device->host,
            'port' => (string) $device->port,
            'api_username' => $device->api_username,
            'api_password' => '',
        ])->assertRedirect(route('devices.index'));

        $device->refresh();
        $this->assertSame('Router Lab (revisi)', $device->name);
        $this->assertSame('password-lama', $device->api_password);
    }

    public function test_admins_can_delete_devices(): void
    {
        $admin = User::factory()->admin()->create();
        $device = Device::factory()->create();

        $this->actingAs($admin)->delete(route('devices.destroy', $device))->assertRedirect(route('devices.index'));

        $this->assertModelMissing($device);
    }
}
