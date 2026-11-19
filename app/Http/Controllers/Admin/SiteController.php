<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Site::withCount(['users', 'assets']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $sites = $query->latest()->paginate(10);

        return view('admin.sites.index', compact('sites'));
    }

    public function create()
    {
        return view('admin.sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'      => ['required', 'string', 'max:50'],
            'name'      => ['required', 'string', 'max:255'],
            'address'   => ['nullable', 'string'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        Site::create($validated);

        return redirect()->route('admin.sites.index')
            ->with('success', "Site {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'code'      => ['required', 'string', 'max:50'],
            'name'      => ['required', 'string', 'max:255'],
            'address'   => ['nullable', 'string'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $site->update($validated);

        return redirect()->route('admin.sites.index')
            ->with('success', "Data Site {$site->name} berhasil diperbarui.");
    }

    public function destroy(Site $site)
    {
        $name = $site->name;
        $site->delete();

        return redirect()->route('admin.sites.index')
            ->with('success', "Site {$name} berhasil dihapus.");
    }
}
