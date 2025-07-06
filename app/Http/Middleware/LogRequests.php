<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log de la petición entrante
        Log::info('🌐 Petición entrante', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'query_params' => $request->query(),
            'body' => $request->all(),
            'files' => $request->allFiles(),
        ]);

        $response = $next($request);

        // Log de la respuesta
        Log::info('📤 Respuesta saliente', [
            'status' => $response->getStatusCode(),
            'url' => $request->fullUrl(),
            'content_type' => $response->headers->get('Content-Type'),
            'content_length' => $response->headers->get('Content-Length'),
        ]);

        return $response;
    }
}
