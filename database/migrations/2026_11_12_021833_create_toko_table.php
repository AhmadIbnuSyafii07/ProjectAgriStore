<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toko', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko', 100);
            $table->string('nama_pemilik', 100);
            $table->string('email', 100)->nullable();
            $table->string('wilayah', 100);
            $table->string('berkas', 150)->nullable();          // contoh: "KTP, NIB (Lengkap)"
            $table->string('status', 20)->default('menunggu');  // menunggu | aktif | ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toko');
    }
};
