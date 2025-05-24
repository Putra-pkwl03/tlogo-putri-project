<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapPresensi extends Model
{
    protected $table = 'rekap_presensi';
    protected $primaryKey = 'id_presensi';

    protected $fillable = [
        'user_id',
        'nama',
        'no_hp',
        'role',
        'tanggal_bergabung',
        'jumlah_kehadiran',
    ];
}
