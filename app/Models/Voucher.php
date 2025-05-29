<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'discount',
    ];
}
