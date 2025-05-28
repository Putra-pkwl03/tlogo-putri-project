<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('history_ticketings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticketing_id'); // Relasi ke ticketings
            $table->string('code_booking');
            $table->string('nama_pemesan');
            $table->string('no_handphone');
            $table->string('email');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('jeep_id');
            $table->unsignedBigInteger('booking_id');

            $table->string('activity'); // created / updated / deleted
            $table->unsignedBigInteger('changed_by')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('ticketing_id')->references('id')->on('ticketings')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jeep_id')->references('jeep_id')->on('jeeps')->onDelete('cascade');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_ticketings');
    }
};
