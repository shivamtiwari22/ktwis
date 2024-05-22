<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryWithVariant extends Model
{
    use SoftDeletes;
    protected $table = 'inventory_with_variants';

    protected $fillable = [
        'title',
        'status',
        'description',
        'slug',
        'meta_title',
        'meta_description',
    ];

    public function variants()
    {
        return $this->hasMany(Variant::class, 'inventory_with_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_id');
    }

    
 
}
