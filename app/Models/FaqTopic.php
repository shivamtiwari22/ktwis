<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqTopic extends Model
{
    use HasFactory, SoftDeletes;

  
    public function faqAnswer()
    {
        return $this->hasMany(FaqAnswer::class , 'faq_topics_id');
    }
    
}
