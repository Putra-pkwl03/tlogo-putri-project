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
            $table->datetime('report_date')->unique();  // <-- unique di sini
            $table->decimal('cash', 25, 2);
            $table->decimal('operational', 25, 2);
            $table->decimal('expenditure', 25, 2);
            $table->decimal('net_cash', 25, 2);
            $table->decimal('clean_operations', 25, 2);
            $table->integer('jeep_amount');
            $table->timestamps();
        
            $table->index('report_date', 'idx_report_date'); // Ini sebenarnya bisa dihapus karena sudah unique
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
