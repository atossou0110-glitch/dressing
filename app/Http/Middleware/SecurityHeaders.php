<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking attacks (Clickjacking protection)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Cross-Site Scripting (XSS) Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // HSTS (HTTP Strict Transport Security)
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy
        if (config('app.env') === 'local') {
            // Development: Allow Vite dev server and HMR via WebSocket + Google Fonts
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: http://localhost:5173 http://127.0.0.1:5173; style-src 'self' 'unsafe-inline' blob: http://localhost:5173 http://127.0.0.1:5173 https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self' ws: wss: http://localhost:5173 http://127.0.0.1:5173; frame-ancestors 'none';";
        } else {
            // Production: Strict CSP but allow Google Fonts + CDN
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self'; frame-ancestors 'none';";
        }
        $response->headers->set('Content-Security-Policy', $csp);

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Feature Policy / Permissions Policy
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');

        // Remove server header
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
