<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['OWNER', 'DRIVER', 'PENGURUS', 'BENDAHARA', 'FO'])->nullable(); // Ubah jika perlu
            $table->string('alamat')->nullable();
            $table->string('no_ktp')->nullable();
            $table->string('telepon')->nullable();
            $table->string('foto_profil')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->string('status')->nullable();
            $table->string('jabatan')->nullable(); // untuk PENGURUS & BENDAHARA
            $table->string('jumlah_jeep')->nullable(); // untuk OWNER
            $table->string('plat_jeep')->nullable(); // untuk DRIVER
            $table->string('foto_jeep')->nullable(); // untuk DRIVER
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};