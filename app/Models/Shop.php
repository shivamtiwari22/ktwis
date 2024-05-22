<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'shop_name',
        'shop_url',
        'legal_name',
        'email',
        'timezone',
        'status_shop',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
