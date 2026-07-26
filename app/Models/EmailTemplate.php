<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasAuditColumns;

class EmailTemplate extends Model
{
    use HasUuids, HasAuditColumns;

    protected $table = 'email_templates';

    protected $fillable = [
        'key',
        'subject',
        'body',
        'description',
    ];
}
