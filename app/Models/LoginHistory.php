<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LoginHistory extends Model
{
    use HasUuids;

    protected $table = 'login_histories';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_type',
        'user_id',
        'email',
        'latitude',
        'longitude',
        'accuracy',
        'city',
        'state',
        'country',
        'timezone',
        'ip_address',
        'browser',
        'operating_system',
        'device_type',
        'screen_resolution',
        'language',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
    ];
}
