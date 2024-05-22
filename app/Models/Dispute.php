<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function disputemessages()
    {
        return $this->hasMany(DisputeMessage::class, 'dispute_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
