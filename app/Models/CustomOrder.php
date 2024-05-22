<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'date',
        'seller_to_customer',
        'terms_condition',
        'reference',
        'order_number',
        'invoice_number',
        'sub_total',
        'discount',
        'shipping',
        'total_amount',
        'status',
        'created_by',
        'payment_status',
        'payment_url',
        'attachments'
    ];
}
