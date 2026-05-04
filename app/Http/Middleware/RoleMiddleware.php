<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memastikan user sudah login dan memiliki role yang sesuai dengan route.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $user = $request->user();
        if (!$user || $user->role !== $role) {
            return abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
