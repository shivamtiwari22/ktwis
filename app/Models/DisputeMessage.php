<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisputeMessage extends Model
{
    use HasFactory;
    protected $table = "disputes_message";

    public function dispute()
    {
        return $this->belongsTo(Dispute::class, 'dispute_id');
    }

    public function response_by()
    {
        return $this->belongsTo(User::class, 'response_by_id');
    }
}
