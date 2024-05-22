<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;
    use HasFactory;

    public function currencyBalance()
    {
        return $this->hasMany(currencyBalance::class, 'currency_id');
    }

    public function wallet()
    {
        return $this->hasMany(userWallet::class, 'currency_id');
    }
}
