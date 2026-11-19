<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::withCount(['users', 'admins', 'sites', 'assets']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $tenants = $query->latest()->paginate(10);

        // Metrik Overview (a2)
        $metrics = [
            'total_tenants'    => Tenant::count(),
            'active_tenants'   => Tenant::where('is_active', true)->count(),
            'inactive_tenants' => Tenant::where('is_active', false)->count(),
            'total_users'      => User::whereNotNull('tenant_id')->count(),
            'total_admins'     => User::where('role', 'admin')->count(),
        ];

        return view('superadmin.tenants.index', compact('tenants', 'metrics'));
    }

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Data Tenant
            'tenant_name'    => ['required', 'string', 'max:255'],
            'tenant_slug'    => ['nullable', 'string', 'max:255', 'unique:tenants,slug'],
            'tenant_email'   => ['nullable', 'email', 'max:255'],
            'tenant_phone'   => ['nullable', 'string', 'max:50'],
            'tenant_address' => ['nullable', 'string'],
            'is_active'      => ['boolean'],

            // Data Akun Admin Awal (f5: tiap tenant pasti membuat 1 user admin)
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:6'],
            'admin_phone'    => ['nullable', 'string', 'max:50'],
            'admin_position' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($validated, &$tenant) {
            $slug = $validated['tenant_slug'] ?: Str::slug($validated['tenant_name']);
            // Pastikan slug unik
            $originalSlug = $slug;
            $count = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            // 1. Buat Tenant
            $tenant = Tenant::create([
                'name'      => $validated['tenant_name'],
                'slug'      => $slug,
                'email'     => $validated['tenant_email'] ?? null,
                'phone'     => $validated['tenant_phone'] ?? null,
                'address'   => $validated['tenant_address'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // 2. Buat Akun Admin Perusahaan Pertama
            User::create([
                'name'        => $validated['admin_name'],
                'email'       => $validated['admin_email'],
                'password'    => Hash::make($validated['admin_password']),
                'role'        => 'admin',
                'tenant_id'   => $tenant->id,
                'is_active'   => true,
                'phone'       => $validated['admin_phone'] ?? null,
                'position'    => $validated['admin_position'] ?? 'Administrator',
                'custom_code' => 'ADM-' . strtoupper(Str::random(5)),
            ]);
        });

        return redirect()->route('superadmin.tenants.index')
            ->with('success', "Tenant {$tenant->name} dan akun Admin berhasil dibuat.");
    }

    public function show(Tenant $tenant)
    {
        $tenant->loadCount(['users', 'admins', 'sites', 'assets']);
        $admins = $tenant->admins()->latest()->get();
        $sites = $tenant->sites()->latest()->get();
        $recentAssets = $tenant->assets()->with(['type.category', 'site', 'user'])->latest()->take(5)->get();

        return view('superadmin.tenants.show', compact('tenant', 'admins', 'sites', 'recentAssets'));
    }

    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:255', 'unique:tenants,slug,' . $tenant->id],
            'email'     => ['nullable', 'email', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'address'   => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $tenant->update($validated);

        return redirect()->route('superadmin.tenants.index')
            ->with('success', "Informasi tenant {$tenant->name} berhasil diperbarui.");
    }

    public function destroy(Tenant $tenant)
    {
        $name = $tenant->name;
        // Trigger cascading delete pada model Tenant
        $tenant->delete();

        return redirect()->route('superadmin.tenants.index')
            ->with('success', "Tenant {$name} dan seluruh data terkait telah berhasil dihapus.");
    }
}
