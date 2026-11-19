<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMutation;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Super Admin Dashboard (a2)
        if ($user->isSuperAdmin()) {
            $metrics = [
                'total_tenants'    => Tenant::count(),
                'active_tenants'   => Tenant::where('is_active', true)->count(),
                'inactive_tenants' => Tenant::where('is_active', false)->count(),
                'total_users'      => User::whereNotNull('tenant_id')->count(),
                'total_admins'     => User::where('role', 'admin')->count(),
            ];

            $recentTenants = Tenant::withCount(['users', 'admins', 'sites', 'assets'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', compact('user', 'metrics', 'recentTenants'));
        }

        // 2. Tenant Admin Dashboard (c4 Overview)
        if ($user->isAdmin()) {
            $tenantId = $user->tenant_id;

            $stats = [
                'total_assets'     => Asset::count(),
                'in_use_assets'    => Asset::where('status', 'in_use')->count(),
                'in_stock_assets'  => Asset::where('status', 'in_stock')->count(),
                'maintenance'      => Asset::where('status', 'maintenance')->count(),
                'total_users'      => User::where('tenant_id', $tenantId)->count(),
                'total_sites'      => Site::count(),
            ];

            $recentAssets = Asset::with(['type.category', 'site', 'user'])
                ->latest()
                ->take(5)
                ->get();

            $recentMutations = AssetMutation::with(['asset', 'toSite', 'toUser'])
                ->latest('mutation_date')
                ->take(5)
                ->get();

            return view('dashboard', compact('user', 'stats', 'recentAssets', 'recentMutations'));
        }

        // 3. Tenant User Dashboard (List barang yang dipakai)
        $myAssets = Asset::where('user_id', $user->id)
            ->with(['type.category', 'site'])
            ->latest()
            ->get();

        return view('dashboard', compact('user', 'myAssets'));
    }
}
