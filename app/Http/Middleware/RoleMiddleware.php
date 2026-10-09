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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, String $role): Response
    {   
        
        $user = $request->user();

        if ($user->role !== $role) {
            return redirect('/division/' . strtolower($user->division->name ?? ''))
                ->with('error', 'You do not have access to that page.');
        }

        return $next($request);
    }
}
