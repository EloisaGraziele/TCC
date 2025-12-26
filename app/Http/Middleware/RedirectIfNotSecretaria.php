<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotSecretaria
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('secretaria')->check()) {
            return redirect()->route('secretaria.login');
        }

        return $next($request);
    }
}