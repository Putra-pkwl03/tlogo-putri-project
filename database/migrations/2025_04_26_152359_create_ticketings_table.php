<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticketings', function (Blueprint $table) {
            $table->id();
            $table->string('code_booking'); // dari order_id di bookings
            $table->string('nama_pemesan');
            $table->string('no_handphone');
            $table->string('email');
            $table->unsignedBigInteger('driver_id'); // relasi ke users
            $table->unsignedBigInteger('jeep_id'); // relasi ke jeeps
            $table->unsignedBigInteger('booking_id'); // relasi ke bookings

            $table->timestamps();

            // Foreign keys
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jeep_id')->references('jeep_id')->on('jeeps')->onDelete('cascade');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticketings');
    }
};