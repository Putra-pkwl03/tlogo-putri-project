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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('booking_id');
            $table->string('order_id', 50);
            $table->decimal('amount', 10, 2);
            $table->string('payment_type', 50)->nullable();
            $table->enum('payment_for', ['dp', 'full'] )->default('dp');
            $table->dateTime('transaction_time')->nullable();
            $table->string('status', 20)->nullable();
            $table->string('payment_gateway', 20)->nullable();
            $table->string('snap_token', 255)->nullable();
            $table->string('redirect_url', 255)->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
