<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Supports single or multiple roles, e.g.:
     *   user.role:admin
     *   user.role:admin,super_admin
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if the user is logged in
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // Check if the account is active
        if ((int) auth()->user()->status !== 1) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN BYPASS
        |--------------------------------------------------------------------------
        |
        | A super_admin is allowed to do EVERYTHING.
        | This guarantees they never get a 403 regardless of the
        | roles listed on the route.
        |
        */
        if (auth()->user()->role === 'super_admin') {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE CHECK
        |--------------------------------------------------------------------------
        |
        | Laravel passes comma-separated middleware parameters as
        | separate variadic arguments when we use `...$roles`.
        | We also fall back to splitting on commas in case the
        | string is passed as a single argument.
        |
        */
        $allowedRoles = [];

        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $r = trim($r);
                if ($r !== '') {
                    $allowedRoles[] = $r;
                }
            }
        }

        if (! in_array(auth()->user()->role, $allowedRoles, true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
