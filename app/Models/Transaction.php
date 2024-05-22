<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function userWallet()
    {
        return $this->belongsTo(userWallet::class, 'receiver_wallet_id');
    }
    

    
}
