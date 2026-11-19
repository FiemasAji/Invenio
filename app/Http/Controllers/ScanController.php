<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Halaman detail aset saat QR code discan lewat kamera smartphone (c2)
     */
    public function show(string $token)
    {
        // Cari aset tanpa global tenant scope agar bisa diakses saat scan QR
        $asset = Asset::withoutGlobalScope('tenant_scope')
            ->where('qr_token', $token)
            ->with(['tenant', 'type.category', 'site', 'user'])
            ->firstOrFail();

        return view('scan.detail', compact('asset'));
    }
}
