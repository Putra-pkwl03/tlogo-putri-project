<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenditureAll extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenditureAllFactory> */
    use HasFactory;
    protected $primaryKey = 'expenditure_id';

    protected $fillable = [
        'salaries_id',
        'issue_date',
        'amount',
        'information',
        'action',
    ];
}
