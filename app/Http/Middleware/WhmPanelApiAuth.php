<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WhmPanelApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('whmpanel.local_api_token');

        if (!$token || hash_equals($token, (string) $request->bearerToken())) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHORIZED',
                'message' => 'Invalid WHMPanel API token',
            ],
        ], 401);
    }
}
