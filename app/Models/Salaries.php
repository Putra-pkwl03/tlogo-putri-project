<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salaries extends Model
{
    protected $table = 'salaries';
    protected $primaryKey = 'salaries_id';
    
    protected $fillable = [
        'salarie_id',
        'nama',
        'role',
        'no_lambung',
        'salarie',
        'total_salary',
        'payment_date',

    ];
}
