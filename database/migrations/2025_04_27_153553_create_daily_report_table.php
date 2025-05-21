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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id('id_daily_report');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('salaries_id');
            $table->string('stomach_no',100)->nullable();
            $table->string('touring_packet', 100)->nullable();
            $table->text('information');
            $table->string('code',100);
            $table->decimal('marketing', 10, 2);
            $table->decimal('cash', 10, 2)->nullable();
            $table->decimal('oop', 10, 2)->nullable();
            $table->decimal('pay_driver', 10, 2)->nullable();
            $table->decimal('total_cash', 10, 2)->nullable();
            $table->integer('amount')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('driver_accept', 10, 2)->nullable();
            $table->decimal('paying_guest', 10, 2)->nullable();
            $table->datetime('arrival_time')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('salaries_id')->references('salaries_id')->on('salaries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report');
    }
};
