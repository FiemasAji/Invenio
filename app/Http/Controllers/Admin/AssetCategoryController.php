<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetCategory::withCount('types');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        AssetCategory::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori barang {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(AssetCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, AssetCategory $category)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori barang {$category->name} berhasil diperbarui.");
    }

    public function destroy(AssetCategory $category)
    {
        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$name} berhasil dihapus.");
    }
}
