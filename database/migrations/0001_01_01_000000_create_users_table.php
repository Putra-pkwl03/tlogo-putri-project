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
            $table->string('password')->unique();
            $table->enum('role', ['Front Office', 'Owner', 'Driver', 'Pengurus']);
            $table->string('alamat')->nullable();
            $table->string('telepon')->unique()->nullable();
            $table->string('foto_profil')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->nullable(); // update di sini
            $table->string('jumlah_jeep')->nullable(); // untuk OWNER
            $table->enum('konfirmasi', ['Bisa', 'Tidak Bisa'])->nullable(); // kolom baru
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
