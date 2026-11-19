<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMutation;
use App\Models\AssetType;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    /**
     * Overview & Multi-Filter Data Inventaris (c4)
     */
    public function index(Request $request)
    {
        $query = Asset::with(['type.category', 'site', 'user']);

        // 1. Search Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // 2. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Filter Site
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // 4. Filter Karyawan / Pemegang
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 5. Filter Kategori
        if ($request->filled('category_id')) {
            $query->whereHas('type', function ($q) use ($request) {
                $q->where('asset_category_id', $request->category_id);
            });
        }

        // 6. Filter Jenis
        if ($request->filled('type_id')) {
            $query->where('asset_type_id', $request->type_id);
        }

        $assets = $query->latest()->paginate(12)->withQueryString();

        // Data untuk Dropdown Filter
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $users = User::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
        $categories = AssetCategory::with('types')->orderBy('name')->get();

        // Ringkasan Statistik Singkat
        $stats = [
            'total'       => Asset::count(),
            'in_use'      => Asset::where('status', 'in_use')->count(),
            'in_stock'    => Asset::where('status', 'in_stock')->count(),
            'maintenance' => Asset::where('status', 'maintenance')->count(),
        ];

        return view('admin.assets.index', compact('assets', 'sites', 'users', 'categories', 'stats'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $users = User::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
        $categories = AssetCategory::with('types.specFields')->orderBy('name')->get();

        return view('admin.assets.create', compact('sites', 'users', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_type_id' => ['required', 'exists:asset_types,id'],
            'asset_code'    => ['required', 'string', 'max:100'],
            'name'          => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'status'        => ['required', 'in:in_use,in_stock,maintenance,disposed'],
            'site_id'       => ['nullable', 'exists:sites,id'],
            'user_id'       => ['nullable', 'exists:users,id'],
            'purchase_date' => ['nullable', 'date'],
            'notes'         => ['nullable', 'string'],
            'specs'         => ['nullable', 'array'],
        ]);

        $asset = Asset::create($validated);

        // Jika langsung di-assign ke user atau site saat dibuat, catat mutasi awal
        if ($asset->site_id || $asset->user_id) {
            AssetMutation::create([
                'tenant_id'     => auth()->user()->tenant_id,
                'asset_id'      => $asset->id,
                'to_site_id'    => $asset->site_id,
                'to_user_id'    => $asset->user_id,
                'mutation_date' => $asset->purchase_date ?: now(),
                'notes'         => 'Pencatatan aset awal ke dalam sistem',
                'created_by'    => auth()->id(),
            ]);
        }

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', "Aset {$asset->name} ({$asset->asset_code}) berhasil didaftarkan.");
    }

    public function show(Asset $asset)
    {
        $asset->load(['type.category', 'site', 'user', 'mutations.fromSite', 'mutations.toSite', 'mutations.fromUser', 'mutations.toUser', 'mutations.creator']);

        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $asset->load('type.specFields');
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        $users = User::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
        $categories = AssetCategory::with('types.specFields')->orderBy('name')->get();

        return view('admin.assets.edit', compact('asset', 'sites', 'users', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_type_id' => ['required', 'exists:asset_types,id'],
            'asset_code'    => ['required', 'string', 'max:100'],
            'name'          => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'status'        => ['required', 'in:in_use,in_stock,maintenance,disposed'],
            'site_id'       => ['nullable', 'exists:sites,id'],
            'user_id'       => ['nullable', 'exists:users,id'],
            'purchase_date' => ['nullable', 'date'],
            'notes'         => ['nullable', 'string'],
            'specs'         => ['nullable', 'array'],
        ]);

        $asset->update($validated);

        return redirect()->route('admin.assets.show', $asset)
            ->with('success', "Data aset {$asset->name} berhasil diperbarui.");
    }

    public function destroy(Asset $asset)
    {
        $name = $asset->name;
        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', "Aset {$name} berhasil dihapus.");
    }

    /**
     * Tampilan cetak label stiker QR Code aset (c2)
     */
    public function printLabel(Asset $asset)
    {
        $asset->load(['type.category', 'site', 'user']);
        return view('admin.assets.print-label', compact('asset'));
    }
}
