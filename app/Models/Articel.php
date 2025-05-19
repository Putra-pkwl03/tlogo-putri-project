<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Articel extends Model
{
    use HasFactory;

    protected $table = 'artikel';
    protected $fillable = [
    'tanggal',
    'judul',
    'pemilik',
    'kategori',
    'isi_konten',
    'gambar'
    ];
}
