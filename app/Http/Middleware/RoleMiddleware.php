<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): 
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Ambil role user dari kolom "role"
        $userRole = $request->user()->role;

        // Kalau role user ga ada di daftar roles yang diizinkan
        if (! in_array($userRole, $roles)) {
            abort(403, 'Anda tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
