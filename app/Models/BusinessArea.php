<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessArea extends Model
{
    use HasFactory;
    use SoftDeletes;


    
    protected $fillable = [
        'name',
        'full_name',
        'iso_code',
        'flag',
        'calling_code',
        'Currency_fk_id',
        'status',
    ];
    
}
