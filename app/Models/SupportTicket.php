<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasAuditColumns;

class SupportTicket extends Model
{
    use HasUuids, HasAuditColumns;

    protected $table = 'support_tickets';

    protected $fillable = [
        'merchant_id',
        'subject',
        'message',
        'status',
        'priority',
        'replies',
    ];

    protected $casts = [
        'replies' => 'json',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
