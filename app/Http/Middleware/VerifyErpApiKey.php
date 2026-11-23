<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyErpApiKey
{
    /**
     * PortalWebsite və xarici servislər üçün X-ERP-API-KEY başlığını yoxlayır.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = (string) config('services.portal_website.token', '');
        $providedToken = (string) ($request->header('X-ERP-API-KEY') ?: $request->bearerToken() ?: '');

        if ($expectedToken === '' || $providedToken === '' || !hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'success' => false,
                'message' => 'API açarı etibarsızdır və ya təqdim edilməyib.',
            ], 401);
        }

        return $next($request);
    }
}

