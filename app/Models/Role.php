<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasUuids;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_has_roles', 'role_id', 'admin_id');
    }
}
