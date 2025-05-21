<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJeepTable extends Migration
{
    public function up(): void
    {
        Schema::create('jeeps', function (Blueprint $table) {
            $table->id('jeep_id'); // Primary Key untuk tabel jeeps
            $table->unsignedBigInteger('users_id'); // FK ke users
            $table->string('no_lambung')->unique();
            $table->string('plat_jeep')->unique();
            $table->string('foto_jeep')->nullable();
            $table->string('merek');
            $table->string('tipe');
            $table->year('tahun_kendaraan');
            $table->string('status')->default('Tersedia');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('jeeps');
    }
}
