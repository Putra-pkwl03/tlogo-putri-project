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
        Schema::create('expenditure_report', function (Blueprint $table) {
            $table->id('expenditure_id');
            $table->unsignedBigInteger('salaries_id');
            $table->dateTime('issue_date')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('information');
            $table->string('action', 255);
            $table->timestamps();

            $table->foreign('salaries_id')->references('salaries_id')->on('salaries')->onDelete('cascade');

            $table->index('issue_date', 'idx_issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenditure_report');
    }
};
