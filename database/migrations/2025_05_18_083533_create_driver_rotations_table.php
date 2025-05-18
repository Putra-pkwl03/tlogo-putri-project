<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDriverRotationsTable extends Migration
{
    public function up()
    {
        Schema::create('driver_rotations', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // tanggal rotasi
            $table->unsignedBigInteger('driver_id'); // relasi ke users
            $table->boolean('assigned')->default(false); // sudah ditugaskan atau belum
            $table->string('skip_reason')->nullable(); // jika batal tugas
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_rotations');
    }
};