<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTypeMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the user is logged in
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // Check if the account is active
        if (auth()->user()->status != 1) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // Check if the user has the correct role
        if (auth()->user()->role != $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
