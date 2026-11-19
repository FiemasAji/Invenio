<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetSpecField extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'asset_type_id',
        'name',
        'field_type',
        'options',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options'     => 'array',
        'is_required' => 'boolean',
        'order'       => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }
}
