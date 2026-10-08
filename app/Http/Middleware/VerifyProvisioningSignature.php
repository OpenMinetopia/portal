<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only the OMT website may call the provisioning API. It signs every request:
 *   X-OMT-Timestamp  unix seconds, accepted within ±300 s
 *   X-OMT-Nonce      a uuid, accepted once
 *   X-OMT-Signature  hex HMAC-SHA256(PORTAL_PROVISIONING_SECRET,
 *                    "{timestamp}\n{nonce}\n{METHOD}\n{path}\n{raw body}")
 * where path is the URL path including /internal/v1, without query string.
 */
class VerifyProvisioningSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('tenancy.provisioning.secret');

        if ($secret === '') {
            return $this->deny('Provisioning is not configured.', 503);
        }

        $timestamp = (string) $request->header('X-OMT-Timestamp');
        $nonce = (string) $request->header('X-OMT-Nonce');
        $signature = (string) $request->header('X-OMT-Signature');

        if (! ctype_digit($timestamp) || ! Str::isUuid($nonce) || $signature === '') {
            return $this->deny('Missing or malformed signature headers.');
        }

        if (abs(now()->getTimestamp() - (int) $timestamp) > config('tenancy.provisioning.max_clock_skew')) {
            return $this->deny('Stale timestamp.');
        }

        $path = '/'.ltrim($request->getPathInfo(), '/');
        $expected = hash_hmac('sha256', implode("\n", [$timestamp, $nonce, strtoupper($request->getMethod()), $path, $request->getContent()]), $secret);

        if (! hash_equals($expected, strtolower($signature))) {
            return $this->deny('Invalid signature.');
        }

        // Only after the signature checks out, so nobody can burn nonces. add() is atomic.
        if (! Cache::store('provisioning')->add('provisioning-nonce:'.strtolower($nonce), true, config('tenancy.provisioning.nonce_ttl'))) {
            return $this->deny('Replayed nonce.');
        }

        return $next($request);
    }

    private function deny(string $message, int $status = 401): Response
    {
        return response()->json(['message' => $message], $status);
    }
}
