<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! filter_var(setting('maintenance_mode', false), FILTER_VALIDATE_BOOLEAN)) {
            return $next($request);
        }

        $path = trim($request->path(), '/');

        $exempt = str_starts_with($path, 'admin/') || $path === 'admin'
            || in_array($path, ['login', 'logout', 'register', 'forgot-password'])
            || ($request->user() && $request->user()->role === 'admin');

        if (! $exempt) {
            abort(503);
        }

        return $next($request);
    }
}
