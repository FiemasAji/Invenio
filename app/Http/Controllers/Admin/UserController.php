<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = User::where('tenant_id', $tenantId)->with('site')->withCount('assets');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('custom_code', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $users = $query->latest()->paginate(10);
        $sites = Site::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:6'],
            'role'        => ['required', Rule::in(['admin', 'user'])],
            'employee_id' => ['nullable', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'position'    => ['nullable', 'string', 'max:100'],
            'site_id'     => ['nullable', 'exists:sites,id'],
            'custom_code' => ['nullable', 'string', 'max:50'],
            'is_active'   => ['boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['tenant_id'] = auth()->user()->tenant_id;

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        // Pastikan tidak edit user luar tenant
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $sites = Site::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'sites'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'    => ['nullable', 'string', 'min:6'],
            'role'        => ['required', Rule::in(['admin', 'user'])],
            'employee_id' => ['nullable', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'position'    => ['nullable', 'string', 'max:100'],
            'site_id'     => ['nullable', 'exists:sites,id'],
            'custom_code' => ['nullable', 'string', 'max:50'],
            'is_active'   => ['boolean'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$name} berhasil dihapus.");
    }
}
