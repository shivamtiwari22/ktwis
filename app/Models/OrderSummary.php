<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSummary extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','shipping_address_id','billing_address_id','payment_method','total_amount','discount_amount','coupon_discount','tax_amount','shipping_charges','grand_total','guarantee_charge'];
}
