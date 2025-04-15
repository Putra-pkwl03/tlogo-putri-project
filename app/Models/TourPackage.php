<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    protected $table = 'tour_packages';
    protected $primaryKey = 'id';
    
    protected $guarded = ['id'];

}
