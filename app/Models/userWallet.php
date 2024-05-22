<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userWallet extends Model
{
    use HasFactory;


    protected $fillable = [
        'dimension',
        'name',
        'status',
        'gallery_images',
        'featured_image',
        'requires_shipping',
        'brand',
        'model_number',
        'slug ',
        'tags',
        'min_order_qty',
        'weight',
        'dimensions',
        'key_features',
        'linked_items',
        'meta_title',
        'meta_description',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'receiver_wallet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currencyBalance(){
        return $this->hasMany(currencyBalance::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }


    
}
