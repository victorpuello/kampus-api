<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // Si estamos en entorno local, no verificar permisos
        if (config('app.env') === 'local') {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user) {
            abort(401, 'Usuario no autenticado');
        }
        if (!$user->hasPermissionTo($permission)) {
            abort(403, 'No tienes permisos para ' . $permission);
        }
        return $next($request);
    }
} 