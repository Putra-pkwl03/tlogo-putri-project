<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    /** @use HasFactory<\Database\Factories\DailyReportFactory> */
    use HasFactory;

    protected $primaryKey = 'id_daily_report';
    protected $guarded = ['id_daily_report'];

    protected $fillable = [
        'booking_id',
        'salaries_id',
        'action',
        'stomach_no',
        'touring_packet',
        'information',
        'code',
        'marketing',
        'cash',
        'oop',
        'pay_driver',
        'total_cash',
        'amount',
        'price',
        'driver_accept',
        'paying_guest',
        'arrival_time'
    ];

}
