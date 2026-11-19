<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // 1. Auto filter by tenant_id untuk user non-superadmin
        static::addGlobalScope('tenant_scope', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                if (! $user->isSuperAdmin() && $user->tenant_id) {
                    $builder->where($builder->getModel()->getTable() . '.tenant_id', $user->tenant_id);
                }
            }
        });

        // 2. Auto assign tenant_id saat create record baru jika belum diset
        static::creating(function (Model $model) {
            if (empty($model->tenant_id) && auth()->check()) {
                $user = auth()->user();
                if ($user->tenant_id) {
                    $model->tenant_id = $user->tenant_id;
                }
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
