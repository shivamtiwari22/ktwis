<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;
  
    protected $table = 'support_tickets';
   
    public function responSupports()
    {
        return $this->hasMany(SupportTicketReply::class, 'support_tickets_id', 'id');

    }



}
