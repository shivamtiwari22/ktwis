<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commision extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_area_fk_id',
        'comission_percentage',
        'status',
    ];
}
