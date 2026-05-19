<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DiagnoseCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        // Log CSRF/session token info for debugging login issues.
        if ($request->getPathInfo() === '/login' && in_array($request->method(), ['GET', 'POST'], true)) {
            $sessionToken = $request->session()->token();
            $postedToken = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');

            $matches = hash_equals($sessionToken ?? '', $postedToken ?? '');

            $sessionCookieName = (string) config('session.cookie');
            $sessionCookie = $sessionCookieName !== '' ? $request->cookies->get($sessionCookieName) : null;

            \Log::debug('CSRF DEBUG', [
                'path' => $request->getPathInfo(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'scheme' => $request->getScheme(),
                'host' => $request->getHost(),
                'port' => $request->getPort(),
                'referer' => $request->headers->get('referer'),
                'session_cookie_name' => $sessionCookieName,
                'session_cookie_present' => $sessionCookie !== null,
                'session_cookie' => $sessionCookie ? substr((string) $sessionCookie, 0, 8) . '...' : null,
                'session_id' => $request->getSession()->getId(),
                'session_token' => $sessionToken ? substr((string) $sessionToken, 0, 10) . '...' : null,
                'posted_token' => $postedToken ? substr((string) $postedToken, 0, 10) . '...' : null,
                'tokens_match' => $matches,
                'session_config' => [
                    'driver' => config('session.driver'),
                    'domain' => config('session.domain'),
                    'secure' => config('session.secure'),
                    'same_site' => config('session.same_site'),
                ],
            ]);
        }

        return $next($request);
    }
}
