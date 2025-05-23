<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJeepTable extends Migration
{
    public function up()
    {
        Schema::create('jeeps', function (Blueprint $table) {
            $table->bigIncrements('jeep_id');
            $table->unsignedBigInteger('users_id'); // Optional, bisa dihapus kalau tidak dipakai lagi
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('driver_id')->nullable();

            $table->string('no_lambung');
            $table->string('plat_jeep');
            $table->string('foto_jeep')->nullable();
            $table->string('merek');
            $table->string('tipe');
            $table->year('tahun_kendaraan');
            $table->string('status')->default('Tersedia');

            $table->timestamps();

            // Foreign keys
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('jeeps');
    }
}
