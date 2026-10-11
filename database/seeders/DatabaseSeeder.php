<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Membuat admin awal HANYA bila ADMIN_EMAIL dan ADMIN_PASSWORD diisi
     * di .env (lihat .env.example). Tidak ada kredensial yang di-hardcode,
     * dan seeder ini tidak melakukan apa pun bila variabel tidak diset.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (filled($email) && filled($password)) {
            User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'Administrator'),
                    'password' => $password, // otomatis di-hash lewat cast
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
