<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'name',
        'dimension',
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

    public function inventory()
    {
        return $this->hasOne(InventoryWithoutVariant::class, 'p_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function productTypes()
    {
        return $this->hasMany(ProductType::class, 'product_id');
    }

    public function inventoryVariants()
    {
        return $this->hasMany(InventoryWithVariant::class, 'p_id');
    }


    protected $appends = ['offer_price'];

    public function getOfferPriceAttribute() {
        $offerPrice = 0; // Initialize the offer price

        // $offerPrice = $this->inventoryVariants->flatMap->variants->first() ?? $this->inventory->first() ;

        if ($this->inventoryVariants) {
            $variants = $this->inventoryVariants->flatMap->variants;
            if ($variants->isNotEmpty()) {
                $offerPrice = $variants->first()->offer_price;
            }
        }
        
        if ($this->inventory) {
            $offerPrice = $this->inventory->offer_price;
        }


        return $offerPrice;
    }



    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }


    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function productcat()
    {
        return $this->hasMany(CategoryProduct::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class,'product_id');
    }


    public function user() {
        return $this->belongsTo(User::class,'created_by');
    }

    public function vendor() {
        return $this->belongsTo(User::class,'created_by');
    }


    public function recentItems()
    {
        return $this->hasMany(RecentItem::class,'product_id');
    }

    public function recentlyView()
    {
        return $this->hasMany(RecentlyViewed::class,'product_id');
    }

    public function specification(){
        return $this->hasOne(Specification::class, 'product_id');
    }

 


  
}
