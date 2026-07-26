<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasAuditColumns;

class ContactRequest extends Model
{
    use HasUuids, HasAuditColumns;

    protected $table = 'contact_requests';

    protected $fillable = [
        'company_name',
        'business_name',
        'full_name',
        'email',
        'phone',
        'country',
        'monthly_volume',
        'business_type',
        'website',
        'message',
        'status',
        'converted_merchant_id',
    ];

    public function convertedMerchant()
    {
        return $this->belongsTo(Merchant::class, 'converted_merchant_id');
    }
}
