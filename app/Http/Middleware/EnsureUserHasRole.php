<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        $user = Auth::user();
        $allowedRoles = array_map('trim', explode(',', $roles));

        if (!$user || !in_array($user->role, $allowedRoles, true)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return $next($request);
    }
}
