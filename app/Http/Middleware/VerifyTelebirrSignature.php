<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyTelebirrSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->headers->get('X-Telebirr-Token');
        $expected = config('services.telebirr.webhook_token');

        if (! $expected || ! hash_equals($expected, (string) $token)) {
            Log::warning('Telebirr webhook rejected due to invalid token.', [
                'ip' => $request->ip(),
            ]);

            abort(Response::HTTP_FORBIDDEN, 'Invalid Telebirr token.');
        }

        return $next($request);
    }
}
