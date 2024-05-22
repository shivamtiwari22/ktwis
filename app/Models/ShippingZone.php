<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;


    public function shippingRates()
    {
        return $this->hasMany(ShippingRate::class, 'zone_id');
    }
}
