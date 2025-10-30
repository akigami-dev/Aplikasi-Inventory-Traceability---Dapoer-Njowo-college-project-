<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        $roles = array_map('strtolower', $role);
        if (!Auth::check() || !in_array(lowercase(Auth::user()->role->nama_role), $roles)) {
             abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
