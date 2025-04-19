<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->string('customer_name', 100);
            $table->string('customer_email', 100);
            $table->string('customer_phone', 20);
            $table->unsignedBigInteger('package_id');
            $table->dateTime('booking_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('tour_date');
            $table->time('start_time');
            $table->decimal('gross_amount', 10, 2);
            $table->string('booking_status', 20)->default('pending');
            $table->string('order_id', 20)->unique();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('payment_type', ['dp', 'full']);
            $table->decimal('dp_amount', 10, 2);
            $table->boolean('is_fully_paid');
            $table->dateTime('due_date');
            $table->boolean('is_refundable');
            $table->string('refund_status', 20);
            $table->string('referral', 50)->nullable();
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('tour_packages')->onDelete('cascade');

            $table->index('booking_status', 'idx_booking_status');
            $table->index('payment_status', 'idx_payment_status');
            $table->index('tour_date', 'idx_tour_date');
            $table->index('order_id', 'idx_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
