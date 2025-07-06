<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $request->bearerToken()) {
            $token = $user->currentAccessToken();

            // Si el token expira en menos de 1 hora, renovarlo
            if ($token && $token->expires_at && $token->expires_at->diffInMinutes(now()) < 60) {
                // Crear un nuevo token
                $newToken = $user->createToken('auth-token', $token->abilities ?? ['*']);

                // Agregar el nuevo token a la respuesta
                $response = $next($request);

                if ($response instanceof \Illuminate\Http\JsonResponse) {
                    $response->headers->set('X-New-Token', $newToken->plainTextToken);
                }

                return $response;
            }
        }

        return $next($request);
    }
}
