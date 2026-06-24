<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ProductInteractionTracker
{
    public const VISITOR_COOKIE_NAME = 'catalog_visitor_token';

    /**
     * Determine whether the current visitor has already voted for a product.
     */
    public function hasVote(Request $request, Product $product): bool
    {
        return $product->votes()
            ->where('visitor_hash', $this->visitorHash($request))
            ->exists();
    }

    /**
     * Determine whether the current visitor has already preordered a product.
     */
    public function hasPreorder(Request $request, Product $product): bool
    {
        return $product->preorders()
            ->where('visitor_hash', $this->visitorHash($request))
            ->exists();
    }

    /**
     * Persist a product vote for the current visitor.
     */
    public function recordVote(Request $request, Product $product): bool
    {
        try {
            $product->votes()->create([
                'visitor_hash' => $this->visitorHash($request),
            ]);

            return true;
        } catch (UniqueConstraintViolationException) {
            return false;
        }
    }

    /**
     * Persist a product preorder for the current visitor.
     */
    public function recordPreorder(Request $request, Product $product): bool
    {
        try {
            $product->preorders()->create([
                'visitor_hash' => $this->visitorHash($request),
            ]);

            return true;
        } catch (UniqueConstraintViolationException) {
            return false;
        }
    }

    /**
     * Resolve the current visitor hash and queue a durable cookie when missing.
     */
    public function visitorHash(Request $request): string
    {
        return hash('sha256', $this->visitorToken($request));
    }

    /**
     * Resolve or create the durable visitor token.
     */
    private function visitorToken(Request $request): string
    {
        $token = (string) $request->cookie(self::VISITOR_COOKIE_NAME);

        if ($token !== '') {
            return $token;
        }

        $token = (string) Str::uuid();

        Cookie::queue(cookie()->forever(
            name: self::VISITOR_COOKIE_NAME,
            value: $token,
            secure: $request->isSecure(),
            httpOnly: true,
            sameSite: 'lax',
        ));

        return $token;
    }
}
