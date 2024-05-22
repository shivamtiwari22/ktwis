<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;
    use HasFactory;

    
    protected $fillable = [
        'language',
        'order',
        'code',
        'flag',
        'php_locale_code',
        'status',
    ];
}
