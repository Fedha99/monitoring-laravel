<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

/**
 * FR-001/FR-002: operator hanya melihat; manajemen perangkat hanya Admin.
 * Terdaftar otomatis lewat konvensi App\Models\Device -> App\Policies\DevicePolicy.
 */
class DevicePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true; // operator boleh melihat daftar (read-only)
    }

    public function view(User $user, Device $device): bool
    {
        return true; // operator boleh melihat detail (read-only)
    }

    public function create(User $user): bool
    {
        return false; // hanya admin (lewat before())
    }

    public function update(User $user, Device $device): bool
    {
        return false; // hanya admin (lewat before())
    }

    public function delete(User $user, Device $device): bool
    {
        return false; // hanya admin (lewat before())
    }
}
