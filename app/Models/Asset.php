<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Asset extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'asset_type_id',
        'site_id',
        'user_id',
        'asset_code',
        'name',
        'serial_number',
        'status',
        'specs',
        'purchase_date',
        'notes',
        'qr_token',
    ];

    protected $casts = [
        'specs'         => 'array',
        'purchase_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (empty($asset->qr_token)) {
                $asset->qr_token = Str::random(32);
            }
        });
    }

    public function type()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mutations()
    {
        return $this->hasMany(AssetMutation::class)->latest('mutation_date');
    }

    /**
     * Generate SVG QR Code URL string for printable label or scanning
     */
    public function getQrCodeSvg(int $size = 180): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString(url('/scan/' . $this->qr_token));
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'in_use'      => 'Dipakai',
            'in_stock'    => 'Tersedia',
            'maintenance' => 'Perbaikan',
            'disposed'    => 'Non-Aktif / Rusak',
            default       => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'in_use'      => 'bg-[#B7D1DA]/50 text-[#222823] border-[#95A3A4]',
            'in_stock'    => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'maintenance' => 'bg-amber-100 text-amber-800 border-amber-300',
            'disposed'    => 'bg-red-100 text-red-800 border-red-300',
            default       => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
