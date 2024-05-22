<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variants';

    protected $fillable = [
        'inventory_with_variant_id',
        'combination',
        'attr_ids',
        'sku',
        'stock_quantity',
        'purchase_price',
        'price',
        'offer_price',
    ];

    public function inventoryWithVariant()
    {
        return $this->belongsTo(InventoryWithVariant::class, 'inventory_with_variant_id');
    }

    public function offer_price(){
            $offerPrices = $this->variants->where;

          // Add logic to calculate the offer price from variants (e.g., find the minimum)
  
          // Update the product's offer price
          $this->offer_price = $offerPrices;
  
          // Save the updated product
          $this->save();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'variant_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'variant_id');
    }
}
