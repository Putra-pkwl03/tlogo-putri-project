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
            $table->unsignedBigInteger('salaries_id')->nullable()->unique(); // UNIK di sini
            $table->dateTime('issue_date');
            $table->decimal('amount', 10, 2);
            $table->text('information')->nullable();
            $table->string('action', 255)->nullable();
            $table->timestamps();
        
            $table->index('issue_date', 'idx_issue_date');
            $table->foreign('salaries_id')->references('salaries_id')->on('salaries')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('expenditure_report', function (Blueprint $table) {
        //     $table->dropForeign(['salaries_id']); // Hapus foreign key salaries_id
        // });
        Schema::dropIfExists('expenditure_report');
    }
};
