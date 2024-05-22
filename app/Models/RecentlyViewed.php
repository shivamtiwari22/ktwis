<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentlyViewed extends Model
{
    use HasFactory;
    protected $table = "recently_viewed";


    public function Product(){
        return $this->belongsTo(Product::class);
    }

    
}
