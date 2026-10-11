<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevicePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_operators_can_view_but_not_manage_devices(): void
    {
        $operator = User::factory()->create();
        $device = Device::factory()->create();

        $this->assertTrue($operator->can('viewAny', Device::class));
        $this->assertTrue($operator->can('view', $device));
        $this->assertFalse($operator->can('create', Device::class));
        $this->assertFalse($operator->can('update', $device));
        $this->assertFalse($operator->can('delete', $device));
    }

    public function test_admins_can_manage_devices(): void
    {
        $admin = User::factory()->admin()->create();
        $device = Device::factory()->create();

        $this->assertTrue($admin->can('create', Device::class));
        $this->assertTrue($admin->can('update', $device));
        $this->assertTrue($admin->can('delete', $device));
    }

    public function test_api_password_is_encrypted_at_rest_and_hidden_from_serialization(): void
    {
        $device = Device::factory()->create(['api_password' => 'super-secret-password']);

        // Nilai mentah di database harus berbeda dari plaintext (NFR-002)
        $raw = $device->fresh()->getRawOriginal('api_password');
        $this->assertNotSame('super-secret-password', $raw);

        // Round-trip dekripsi untuk pemakaian internal worker (M2)
        $this->assertSame('super-secret-password', $device->fresh()->api_password);

        // NFR-002: tidak boleh muncul pada serialisasi apapun
        $this->assertArrayNotHasKey('api_password', $device->fresh()->toArray());
    }
}
