<?php

namespace App\Models\Scopes;

trait HasTenant
{
    /**
     * Boot the HasTenant trait for a model.
     *
     * @return void
     */
    protected static function bootHasTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (auth()->hasUser() && auth()->user()->role === 'library_owner' && !$model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}