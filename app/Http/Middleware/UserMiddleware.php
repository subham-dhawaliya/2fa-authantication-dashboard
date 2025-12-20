<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Allow both user and admin (admin has all access)
        if (!Auth::user()->isUser() && !Auth::user()->isAdmin()) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
