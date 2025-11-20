<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS request first
        if ($request->isMethod('OPTIONS')) {
            return $this->handlePreflight($request);
        }

        // Process the actual request
        $response = $next($request);

        // Add CORS headers to response
        return $this->addCorsHeaders($request, $response);
    }

    private function handlePreflight(Request $request): Response
    {
        $origin = $request->header('Origin');

        return response('', 200)
            ->header('Access-Control-Allow-Origin', $origin ?: '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Max-Age', '86400');
    }

    private function addCorsHeaders(Request $request, Response $response): Response
    {
        $origin = $request->header('Origin');

        return $response
            ->header('Access-Control-Allow-Origin', $origin ?: '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}