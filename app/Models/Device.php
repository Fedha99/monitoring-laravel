<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'host',
        'port',
        'api_username',
        'api_password',
        'monitoring_enabled',
        'metadata',
    ];

    /**
     * NFR-002: kredensial tidak boleh bocor ke browser/log.
     */
    protected $hidden = [
        'api_password',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'monitoring_enabled' => 'boolean',
            'metadata' => 'array',
            'api_password' => 'encrypted',
        ];
    }
}
