<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class currencyBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'currency_id',
        'balance_amount',
    ];
    

    public function wallet(){
        return $this->belongsTo(userWallet::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
}
