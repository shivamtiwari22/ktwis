<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['state_name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }

    public function useraddresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
