<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeReport extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeReportFactory> */
    use HasFactory;
    protected $table = 'income_report';
    protected $primaryKey = 'income_id';

    protected $fillable = [
        'booking_id',
        'ticketing_id',
        'expenditure_id',
        'booking_date',
        'income',
        'expediture',
        'cash',
    ];
}
