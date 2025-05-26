<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id('salaries_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('ticketing_id');
            $table->string('nama');
            $table->string('role');
            $table->string('no_lambung');
            $table->float('kas')->default(0);
            $table->float('operasional')->default(0);
            $table->float('salarie');
            $table->float('total_salary');
            $table->date('payment_date');
            $table->enum('status', ['belum', 'diterima'])->default('belum');
            $table->timestamps();

            $table->foreign('ticketing_id')->references('id')->on('ticketings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
