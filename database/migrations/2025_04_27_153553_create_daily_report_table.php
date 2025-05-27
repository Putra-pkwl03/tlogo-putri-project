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
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('salaries_id')->nullable()->unique(); // unique di sini
            $table->string('stomach_no', 100);
            $table->string('touring_packet', 100);
            $table->text('information')->nullable();
            $table->string('code', 100)->nullable();
            $table->decimal('marketing', 10, 2)->nullable();
            $table->decimal('cash', 10, 2);
            $table->decimal('oop', 10, 2);
            $table->decimal('pay_driver', 10, 2);
            $table->decimal('total_cash', 10, 2);
            $table->integer('amount');
            $table->decimal('price', 10, 2);
            $table->decimal('driver_accept', 10, 2);
            $table->decimal('paying_guest', 10, 2);
            $table->datetime('arrival_time');
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
        // Schema::table('daily_reports', function (Blueprint $table) {
        //     $table->dropForeign(['booking_id']); // Hapus foreign key booking_id
        //     $table->dropForeign(['salaries_id']); // Hapus foreign key salaries_id
        // });
        Schema::dropIfExists('daily_report');
    }
};