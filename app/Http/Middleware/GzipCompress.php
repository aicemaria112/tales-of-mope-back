<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipCompress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if the client supports gzip
        if ($request->header('Accept-Encoding') && str_contains($request->header('Accept-Encoding'), 'gzip')) {
            // Compress the response content
            $response->header('Content-Encoding', 'gzip');
            $response->setContent(gzencode($response->getContent(), 5));
        }

        return $response;
    }
}
