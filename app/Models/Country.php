<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['country_name'];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class);
    }
}
