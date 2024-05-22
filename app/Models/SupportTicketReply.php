<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    use HasFactory;
    protected $table = 'support_ticket_replies';

    public function responSupports()
    {
        return $this->belongsToMany(SupportTicket::class, 'support_tickets_id');
    }



}
