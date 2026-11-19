<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Tenant $tenant) {
            // Cascading clean deletion: hapus semua user, site, kategori, jenis, aset, dan mutasi
            $tenant->users()->each(fn($u) => $u->delete());
            $tenant->assets()->each(fn($a) => $a->delete());
            $tenant->types()->each(fn($t) => $t->delete());
            $tenant->categories()->each(fn($c) => $c->delete());
            $tenant->sites()->each(fn($s) => $s->delete());
            $tenant->mutations()->delete();
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function categories()
    {
        return $this->hasMany(AssetCategory::class);
    }

    public function types()
    {
        return $this->hasMany(AssetType::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function mutations()
    {
        return $this->hasMany(AssetMutation::class);
    }
}
