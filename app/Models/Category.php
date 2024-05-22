<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_category_id')->with('children');
    }
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category_id')->with('parent');
    }
    public function deleteCategoryHierarchy($categoryId)
    {
        $category = Category::with('children')->find($categoryId);

        if ($category) {
            $category->children()->each(function ($childCategory) {
                $this->deleteCategoryHierarchy($childCategory->id);
            });

            $category->delete();
        }
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_category');
    }

    public function categoryProducts()
    {
        return $this->hasMany(CategoryProduct::class);
    }

    public function returnPolicy(){
        return $this->hasOne(ReturnPolicy::class ,'category_id');
    }
    public function category()
{
    return $this->belongsTo(Category::class);
}

}
