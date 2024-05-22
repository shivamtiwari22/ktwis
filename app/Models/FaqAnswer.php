<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionFunctionAbstract;

class FaqAnswer extends Model
{
    use HasFactory,SoftDeletes;

    public function faqTopic(){
           return $this->belongsTo(FaqTopic::class,);
    }
}
