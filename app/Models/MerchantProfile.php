<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MerchantProfile extends Model
{
    use HasUuids;

    protected $table = 'merchant_profiles';

    protected $fillable = [
        'merchant_id',
        'gst',
        'pan',
        'bank_name',
        'bank_account_number',
        'bank_ifsc',
        'bank_holder_name',
        'kyc_document_path',
        'profile_image_path',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
