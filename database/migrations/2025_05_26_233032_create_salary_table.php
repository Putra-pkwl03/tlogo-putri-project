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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id('salaries_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('ticketing_id')->nullable();
            $table->string('nama');
            $table->string('role');
            $table->string('no_lambung')->nullable(); 
            $table->float('kas')->default(0)->nullable(); 
            $table->float('operasional')->default(0)->nullable();
            $table->float('salarie')->nullable();
            $table->float('total_salary')->nullable();
            $table->date('payment_date')->nullable(); 
            $table->enum('status', ['belum', 'diterima'])->default('belum');
            $table->timestamps();

            $table->foreign('ticketing_id')->references('id')->on('ticketings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary');
    }
};