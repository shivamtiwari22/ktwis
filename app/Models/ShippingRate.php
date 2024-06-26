<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ShippingRate extends Model
{
    use HasFactory,SoftDeletes;


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function shippingRates()
{
    return $this->hasMany(ShippingRate::class, 'zone_id');  
}

}
