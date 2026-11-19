<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantScope
{
    /**
     * Memastikan admin dan user ter-lock ke tenant mereka sendiri.
     * Super admin bebas mengakses semua tenant.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isSuperAdmin()) {
            if (! $user->tenant_id || ! $user->tenant || ! $user->tenant->is_active) {
                auth()->logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda tidak terkait dengan tenant aktif.']);
            }

            // Simpan tenant_id ke session supaya bisa dipakai di seluruh request
            $request->session()->put('tenant_id', $user->tenant_id);
        }

        return $next($request);
    }
}
