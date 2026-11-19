<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetType extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'asset_category_id',
        'code',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function specFields()
    {
        return $this->hasMany(AssetSpecField::class)->orderBy('order');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
