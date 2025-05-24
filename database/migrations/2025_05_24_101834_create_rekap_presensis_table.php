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
        Schema::create('rekap_presensi', function (Blueprint $table) {
            $table->id('id_presensi');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_lengkap');
            $table->string('no_hp')->nullable();
            $table->string('role')->nullable();
            $table->dateTime('tanggal_bergabung')->nullable();
            $table->unsignedTinyInteger('bulan'); 
            $table->unsignedSmallInteger('tahun');
            $table->integer('jumlah_kehadiran')->default(0);
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        
            // Composite unique index
            $table->unique(['user_id', 'bulan', 'tahun']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_presensis');
    }
};
