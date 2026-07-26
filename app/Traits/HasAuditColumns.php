<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuditColumns
{
    protected static function bootHasAuditColumns()
    {
        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = static::getAuditUserId();
            }
        });

        static::updating(function ($model) {
            if (empty($model->updated_by)) {
                $model->updated_by = static::getAuditUserId();
            }
        });

        if (method_exists(static::class, 'restoring')) {
            static::deleting(function ($model) {
                if (empty($model->deleted_by)) {
                    $model->deleted_by = static::getAuditUserId();
                    $model->save();
                }
            });
        }
    }

    protected static function getAuditUserId(): ?string
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user()->id;
        }

        if (Auth::guard('merchant')->check()) {
            return Auth::guard('merchant')->user()->id;
        }

        return null;
    }
}
