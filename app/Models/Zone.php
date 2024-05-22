<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function zone_country()
    {
        return $this->hasMany(ZoneCountryState::class, 'zone_id');
    }
}