<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBranchApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('services.branch.api_key');
        $providedKey = $request->header('X-API-Key');

        if (
            ! is_string($expectedKey) ||
            $expectedKey === '' ||
            ! is_string($providedKey) ||
            ! hash_equals($expectedKey, $providedKey)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'API Key inválida o no proporcionada.',
            ], 401);
        }

        return $next($request);
    }
}
