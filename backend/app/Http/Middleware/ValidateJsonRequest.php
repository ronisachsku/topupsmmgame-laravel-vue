<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            if ($request->header('Content-Type') !== 'application/json' && !$request->hasFile('avatar')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Content-Type must be application/json'
                ], 415);
            }

            // Validate JSON syntax
            if ($request->getContent() && !$request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format'
                ], 400);
            }
        }

        return $next($request);
    }
}
