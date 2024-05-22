<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoneCountryState extends Model
{
    use HasFactory,SoftDeletes;

    public function country()
    {
        return $this->belongsTo(Country::class,"country_id");
    }

    public function state()
    {
        return $this->belongsTo(State::class,"state_id");   
    }
}
