<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class disputeText extends Model
{
    use HasFactory;

    protected $fillable=[
        'dispute_right_text',
        'dispute_left_text',
    ];
}
