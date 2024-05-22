<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory,SoftDeletes;
    
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class,'attribute_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'attribute_category');
    }

    
}

