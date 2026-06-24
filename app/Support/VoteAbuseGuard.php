<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class VoteAbuseGuard
{
    public function __construct(
        private readonly ProductInteractionTracker $interactionTracker,
    ) {}

    /**
     * Resolve a blocking payload when vote abuse protections should reject the request.
     *
     * @return array{status: int, message: string}|null
     */
    public function blockPayload(Request $request, Product $product): ?array
    {
        if (! (bool) config('catalog.vote_guard.enabled', true)) {
            return null;
        }

        $fingerprint = $this->ipFingerprint($request);

        if ($fingerprint === null) {
            return null;
        }

        // When vote counters are reset to zero, start a new anti-abuse window.
        if ((int) $product->vote_count === 0) {
            return null;
        }

        $attemptLimit = max(3, (int) config('catalog.vote_guard.max_attempts_per_hour', 20));
        $attemptKey = "catalog:vote:attempt:{$product->id}:{$fingerprint}";

        if (RateLimiter::tooManyAttempts($attemptKey, $attemptLimit)) {
            $availableIn = max(1, (int) ceil(RateLimiter::availableIn($attemptKey) / 60));

            return [
                'status' => 429,
                'message' => "Trop de tentatives de vote. Reessayez dans {$availableIn} minute(s).",
            ];
        }

        RateLimiter::hit($attemptKey, 3600);

        $cooldownHours = max(1, (int) config('catalog.vote_guard.ip_cooldown_hours', 24));
        $cooldownKey = "catalog:vote:cooldown:{$product->id}:{$fingerprint}";
        $visitorHash = $this->interactionTracker->visitorHash($request);

        if (Cache::has($cooldownKey)) {
            $storedVisitorHash = (string) Cache::get($cooldownKey, '');

            if ($storedVisitorHash !== '' && hash_equals($storedVisitorHash, $visitorHash)) {
                return null;
            }

            return [
                'status' => 429,
                'message' => "Ce produit a deja recu un vote depuis cette adresse reseau dans les {$cooldownHours} dernieres heures.",
            ];
        }

        return null;
    }

    /**
     * Persist a short-term lock after a successful vote to reduce scripted abuse.
     */
    public function rememberSuccessfulVote(Request $request, Product $product): void
    {
        if (! (bool) config('catalog.vote_guard.enabled', true)) {
            return;
        }

        $fingerprint = $this->ipFingerprint($request);

        if ($fingerprint === null) {
            return;
        }

        $cooldownHours = max(1, (int) config('catalog.vote_guard.ip_cooldown_hours', 24));
        $cooldownKey = "catalog:vote:cooldown:{$product->id}:{$fingerprint}";
        $visitorHash = $this->interactionTracker->visitorHash($request);

        Cache::put($cooldownKey, $visitorHash, now()->addHours($cooldownHours));
    }

    /**
     * Build a stable fingerprint for the current network origin.
     */
    private function ipFingerprint(Request $request): ?string
    {
        $ipAddress = trim((string) $request->ip());

        if ($ipAddress === '') {
            return null;
        }

        return hash('sha256', $ipAddress.'|'.(string) config('app.key'));
    }
}
