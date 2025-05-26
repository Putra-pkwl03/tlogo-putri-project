<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $table = 'report';
    protected $primaryKey = 'report_id';
    protected $guarded = ['report_id'];

    protected $fillable = [
        'income_id',
        'expenditure_id',
        'report_date',
        'cash',
        'operational',
        'expenditure',
        'net_cash',
        'clean_operations',
        'jeep_amount',
        
    ];
}
