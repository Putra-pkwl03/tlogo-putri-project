<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenditureReport extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenditureReportFactory> */
    use HasFactory;
    protected $table = 'expenditure_report';
    protected $primaryKey = 'expenditure_id';

    protected $fillable = [
        'salaries_id',
        'issue_date',
        'amount',
        'information',
        'action',
    ];
}
