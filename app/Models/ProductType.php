<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function scopeActiveVendorProducts($query)
    {
        return $query->whereHas('product.vendor.shops', function ($query) {
            $query->where('status', 'active');
        });
    }
}
