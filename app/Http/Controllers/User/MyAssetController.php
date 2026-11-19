<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class MyAssetController extends Controller
{
    /**
     * Tampilan list barang apa saja yang dipakai oleh user login (dashboard user)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Asset::where('user_id', $user->id)
            ->with(['type.category', 'site', 'mutations']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $myAssets = $query->latest()->paginate(10);

        return view('user.my-assets', compact('myAssets', 'user'));
    }
}
