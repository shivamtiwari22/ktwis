<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_type',
        'contact_person',
        'address',
        'floor_apartment',
        'country',
        'state',
        'zip_code',
        'city',
        'contact_no',
        'country_code', 
        'is_default',
        'is_current'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function states()
    {
        return $this->belongsTo(State::class,'state');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }
    
    public function useres()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
