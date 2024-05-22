<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','user_id','guest_user'];

    public function Product(){
        return $this->belongsTo(Product::class);
    }
}
