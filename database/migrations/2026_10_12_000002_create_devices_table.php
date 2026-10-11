<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FR-002: data minimal perangkat — nama, host/IP, port API-SSL,
     * referensi credential terenkripsi, metadata, dan status aktif.
     *
     * Catatan: kolom status (online/offline/unknown/stale) sengaja BELUM
     * dibuat karena threshold-nya menunggu keputusan D-014; status akan
     * diturunkan dari hasil polling pada milestone M2.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(8729); // port default API-SSL
            $table->string('api_username')->nullable();
            $table->text('api_password')->nullable(); // disimpan terenkripsi (NFR-002)
            $table->boolean('monitoring_enabled')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['host', 'port']); // identitas target unik (FR-003 allowlist)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
