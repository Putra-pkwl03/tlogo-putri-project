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
        Schema::create('expenditure_all', function (Blueprint $table) {
            $table->id('expenditure_id');
            $table->unsignedBigInteger('salaries_id')->nullable();
            $table->dateTime('issue_date');
            $table->decimal('amount', 10, 2);
            $table->text('information')->nullable();
            $table->string('action', 255)->nullable();
            $table->timestamps();

            $table->index('issue_date', 'idx_issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenditure_alls');
    }
};
