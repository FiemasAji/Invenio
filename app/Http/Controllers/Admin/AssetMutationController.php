<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetMutation;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetMutationController extends Controller
{
    /**
     * Audit Log Seluruh Riwayat Mutasi Barang (c3)
     */
    public function index(Request $request)
    {
        $query = AssetMutation::with(['asset.type', 'fromSite', 'toSite', 'fromUser', 'toUser', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('mutation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('mutation_date', '<=', $request->date_to);
        }

        $mutations = $query->latest('mutation_date')->latest('id')->paginate(15)->withQueryString();

        return view('admin.mutations.index', compact('mutations'));
    }

    /**
     * Form Mutasi Barang (c3)
     */
    public function create(Request $request)
    {
        $selectedAsset = null;
        if ($request->filled('asset_id')) {
            $selectedAsset = Asset::with(['site', 'user', 'type'])->findOrFail($request->asset_id);
        }

        $assets = Asset::with(['site', 'user', 'type'])->orderBy('name')->get();
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $users = User::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();

        return view('admin.mutations.create', compact('assets', 'sites', 'users', 'selectedAsset'));
    }

    /**
     * Eksekusi Mutasi Barang & Update Lokasi/Pemegang Aset (c3)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id'      => ['required', 'exists:assets,id'],
            'to_site_id'    => ['nullable', 'exists:sites,id'],
            'to_user_id'    => ['nullable', 'exists:users,id'],
            'mutation_date' => ['required', 'date'],
            'status'        => ['required', 'in:in_use,in_stock,maintenance,disposed'],
            'notes'         => ['required', 'string', 'max:500'],
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        DB::transaction(function () use ($validated, $asset) {
            // 1. Rekam riwayat mutasi
            AssetMutation::create([
                'tenant_id'     => auth()->user()->tenant_id,
                'asset_id'      => $asset->id,
                'from_site_id'  => $asset->site_id,
                'to_site_id'    => $validated['to_site_id'] ?? null,
                'from_user_id'  => $asset->user_id,
                'to_user_id'    => $validated['to_user_id'] ?? null,
                'mutation_date' => $validated['mutation_date'],
                'notes'         => $validated['notes'],
                'created_by'    => auth()->id(),
            ]);

            // 2. Perbarui data lokasi fisik dan user pemegang aset
            $asset->update([
                'site_id' => $validated['to_site_id'] ?? null,
                'user_id' => $validated['to_user_id'] ?? null,
                'status'  => $validated['status'],
            ]);
        });

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', "Mutasi aset {$asset->name} ({$asset->asset_code}) berhasil dicatat.");
    }
}
