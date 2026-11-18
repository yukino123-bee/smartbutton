<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Device-facing API guard. When DEVICE_API_TOKEN is set, requests must send it as X-Device-Token.
 * With no token configured the API stays open (local development).
 */
class DeviceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.device.token');

        // Fail closed in production: an unset token must not leave the alert API open to anyone.
        abort_if($expected === '' && app()->isProduction(), 503, 'Device API token is not configured.');

        if ($expected !== '' && ! hash_equals($expected, (string) $request->header('X-Device-Token'))) {
            abort(401, 'Invalid device token.');
        }

        return $next($request);
    }
}
