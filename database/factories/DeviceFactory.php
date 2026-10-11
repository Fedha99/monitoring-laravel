<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Router '.$this->faker->city(),
            'host' => $this->faker->unique()->ipv4(),
            'port' => 8729,
            'api_username' => 'mcc-readonly',
            // plaintext di sini; cast 'encrypted' menyimpannya terenkripsi di DB
            'api_password' => $this->faker->password(16),
            'monitoring_enabled' => false,
            'metadata' => null,
        ];
    }

    public function monitoringEnabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'monitoring_enabled' => true,
        ]);
    }
}
