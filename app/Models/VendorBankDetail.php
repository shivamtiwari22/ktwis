<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'account_holder_name' ,
        'account_number' ,
        'account_type' ,
        'routing_number' ,
        'bic_code' ,
        'iban_number' ,
        'bank_address' ,
        'vendor_id' ,
    ];
  

}
