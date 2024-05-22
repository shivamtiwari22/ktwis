<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'address_line1' ,
        'address_line2' ,
        'city' ,
        'postal_code',
        'phone',
        'state',
        'vendor_id' ,
        'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
