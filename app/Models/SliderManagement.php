<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SliderManagement extends Model
{
    use HasFactory;

        
    protected $fillable = [
        'title',
        'title_color',
        'subtitle',
        'subtitle_color',
        'description',
        'description_color',
        'link',
        'order',
        'text_position',
        'slider_image',
        'mobile_image',
        'status',
    ];
}
