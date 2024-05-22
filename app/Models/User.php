<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'web';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nickname',
        'mobile_number',
        'country_code',
        'fcm_token',
        'auth_provider',
        'device_id',
        'device_type',
        'user_identifier',
        'created_by',
        'userID'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles->contains('role', $role);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'created_by');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function customerCart()
    {
        return $this->hasMany(Cart::class, 'seller_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function seller()
    {
        return $this->hasMany(Vendor::class, 'seller_id');
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class, 'customer_id');
    }

    public function vendor_disputes()
    {
        return $this->hasMany(Dispute::class, 'vendor_id');
    }

    public function wallet()
    {
        return $this->hasOne(UserWallet::class, 'user_id');
    }

    public function shops()
    {
        return $this->hasOne(Shop::class, 'vendor_id');
    }

   

    public function reviews()
    {
        return $this->hasMany(Review::class , 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function products() {
        return $this->hasMany(Product::class ,'created_by');
    }

    public function shipping() {
        return $this->hasOne(ShippingRate::class ,'created_by');
    }

    public function userAddress(){
        return $this->hasMany(UserAddress::class , 'user_id');
    }
    public function address() {
        return $this->hasMany(UserAddress::class ,'user_id');
    }


    public function shopAddress()
    {
        return $this->hasOne(VendorAddress::class, 'vendor_id');
    }
}
