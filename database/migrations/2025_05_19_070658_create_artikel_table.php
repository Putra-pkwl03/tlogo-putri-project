<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtikelTable extends Migration
{
    public function up()
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->id(); // Sama dengan INT AUTO_INCREMENT PRIMARY KEY
            $table->date('tanggal'); // DATE NOT NULL
            $table->string('judul', 255); // VARCHAR(255) NOT NULL
            $table->string('pemilik', 100)->nullable(); // VARCHAR(100) bisa NULL
            $table->text('kategori')->nullable(); // VARCHAR(100) bisa NULL
            $table->text('isi_konten')->nullable(); // TEXT bisa NULL
            $table->string('gambar', 255)->nullable(); // VARCHAR(255) bisa NULL
            $table->timestamps(); // created_at dan updated_at
            $table->string('status', 255)->nullable(); // VARCHAR(255) bisa NULL
        });
    }

    public function down()
    {
        Schema::dropIfExists('artikel');
    }
};

