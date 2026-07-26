<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasAuditColumns;

class Commission extends Model
{
    use HasUuids, HasAuditColumns;

    protected $table = 'commissions';

    protected $fillable = [
        'merchant_id',
        'name',
        'type',
        'fixed_charge',
        'percentage_charge',
        'slab_rates',
        'min_charge',
        'max_charge',
        'gst_rate',
        'effective_date',
        'is_active',
    ];

    protected $casts = [
        'fixed_charge' => 'decimal:4',
        'percentage_charge' => 'decimal:2',
        'slab_rates' => 'json',
        'min_charge' => 'decimal:4',
        'max_charge' => 'decimal:4',
        'gst_rate' => 'decimal:2',
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
