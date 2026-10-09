<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DivisionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return $next($request);
        }

        $own = strtolower($user->division->name ?? '');

        if ($own !== $request->route('name')) {
            return redirect('/division/' . $own)
                ->with('error', 'You do not have access to that division.');
        }

        return $next($request);
    }
}
