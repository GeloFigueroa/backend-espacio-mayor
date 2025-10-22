<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // 🔍 Diagnóstico temporal
        \Log::info('⚠️ Middleware AUTH ejecutado en: '.$request->path());
        return $next($request);
    }

    protected function redirectTo($request)
    {
        // Evita la redirección al "login"
        return null;
    }
}
