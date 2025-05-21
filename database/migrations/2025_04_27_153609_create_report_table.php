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
        Schema::create('report', function (Blueprint $table) {
            $table->id('report_id');
            $table->unsignedBigInteger('income_id');
            $table->unsignedBigInteger('expenditure_id');
            $table->datetime('report_date')->nullable();
            $table->decimal('cash', 10, 2)->nullable();
            $table->decimal('operational', 10, 2)->nullable();
            $table->decimal('expenditure', 10, 2)->nullable();
            $table->decimal('net_cash', 10, 2)->nullable();
            $table->decimal('clean_operations', 10, 2)->nullable();
            $table->integer('jeep_amount')->nullable();
            $table->timestamps();

            $table->index('report_date', 'idx_report_date');
            $table->foreign('income_id')->references('income_id')->on('income_report')->onDelete('cascade');
            $table->foreign('expenditure_id')->references('expenditure_id')->on('expenditure_report')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
