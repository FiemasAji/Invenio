<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\AssetSpecField;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetType::with(['category', 'specFields'])->withCount('assets');

        if ($request->filled('category_id')) {
            $query->where('asset_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $types = $query->latest()->paginate(10);
        $categories = AssetCategory::orderBy('name')->get();

        return view('admin.types.index', compact('types', 'categories'));
    }

    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('admin.types.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'code'              => ['required', 'string', 'max:50'],
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],

            // Dynamic Custom Spec Fields (b3)
            'specs'             => ['nullable', 'array'],
            'specs.*.name'      => ['required_with:specs', 'string', 'max:100'],
            'specs.*.type'      => ['required_with:specs', 'in:text,number,select'],
            'specs.*.options'   => ['nullable', 'string'], // Koma-separated: "Windows, Linux, macOS"
            'specs.*.required'  => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            $type = AssetType::create([
                'asset_category_id' => $validated['asset_category_id'],
                'code'              => $validated['code'],
                'name'              => $validated['name'],
                'description'       => $validated['description'] ?? null,
            ]);

            if (! empty($validated['specs'])) {
                foreach ($validated['specs'] as $index => $spec) {
                    if (empty($spec['name'])) continue;

                    $options = null;
                    if ($spec['type'] === 'select' && ! empty($spec['options'])) {
                        $options = array_map('trim', explode(',', $spec['options']));
                    }

                    AssetSpecField::create([
                        'tenant_id'     => auth()->user()->tenant_id,
                        'asset_type_id' => $type->id,
                        'name'          => $spec['name'],
                        'field_type'    => $spec['type'],
                        'options'       => $options,
                        'is_required'   => ! empty($spec['required']),
                        'order'         => $index,
                    ]);
                }
            }
        });

        return redirect()->route('admin.types.index')
            ->with('success', "Jenis barang {$validated['name']} beserta parameter spesifikasi berhasil dibuat.");
    }

    public function edit(AssetType $type)
    {
        $type->load(['specFields']);
        $categories = AssetCategory::orderBy('name')->get();
        return view('admin.types.edit', compact('type', 'categories'));
    }

    public function update(Request $request, AssetType $type)
    {
        $validated = $request->validate([
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'code'              => ['required', 'string', 'max:50'],
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],

            'specs'             => ['nullable', 'array'],
            'specs.*.name'      => ['required_with:specs', 'string', 'max:100'],
            'specs.*.type'      => ['required_with:specs', 'in:text,number,select'],
            'specs.*.options'   => ['nullable', 'string'],
            'specs.*.required'  => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $type) {
            $type->update([
                'asset_category_id' => $validated['asset_category_id'],
                'code'              => $validated['code'],
                'name'              => $validated['name'],
                'description'       => $validated['description'] ?? null,
            ]);

            // Re-sync spec fields
            $type->specFields()->delete();

            if (! empty($validated['specs'])) {
                foreach ($validated['specs'] as $index => $spec) {
                    if (empty($spec['name'])) continue;

                    $options = null;
                    if ($spec['type'] === 'select' && ! empty($spec['options'])) {
                        $options = array_map('trim', explode(',', $spec['options']));
                    }

                    AssetSpecField::create([
                        'tenant_id'     => auth()->user()->tenant_id,
                        'asset_type_id' => $type->id,
                        'name'          => $spec['name'],
                        'field_type'    => $spec['type'],
                        'options'       => $options,
                        'is_required'   => ! empty($spec['required']),
                        'order'         => $index,
                    ]);
                }
            }
        });

        return redirect()->route('admin.types.index')
            ->with('success', "Data jenis barang {$type->name} berhasil diperbarui.");
    }

    public function destroy(AssetType $type)
    {
        $name = $type->name;
        $type->delete();

        return redirect()->route('admin.types.index')
            ->with('success', "Jenis barang {$name} berhasil dihapus.");
    }

    /**
     * JSON Endpoint untuk mengambil daftar spec fields saat admin memilih jenis barang di form aset
     */
    public function getSpecs(AssetType $type)
    {
        return response()->json($type->specFields()->orderBy('order')->get());
    }
}
