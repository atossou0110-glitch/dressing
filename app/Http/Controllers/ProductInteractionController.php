<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SiteSetting;
use App\Support\ProductInteractionTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductInteractionController extends Controller
{
    public function __construct(
        private readonly ProductInteractionTracker $interactionTracker,
    ) {}

    /**
     * Persist a preorder for a product.
     */
    public function preorder(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->isStorefrontVisible(), 404);

        $whatsAppUrl = $this->whatsAppUrlForProduct($product);

        if ($whatsAppUrl === null) {
            return response()->json([
                'message' => 'Configurez WHATSAPP_NUMBER pour activer les precommandes via WhatsApp.',
                'product' => $this->productState($request, $product->fresh()),
            ], 422);
        }

        if ($this->interactionTracker->recordPreorder($request, $product)) {
            $product->increment('preorder_count');
        }

        return response()->json([
            'message' => 'Redirection vers WhatsApp.',
            'product' => $this->productState($request, $product->fresh()),
            'whatsappUrl' => $whatsAppUrl,
        ]);
    }

    /**
     * Store a review for the product.
     */
    public function storeReview(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->isStorefrontVisible(), 404);

        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:80'],
            'body' => ['required', 'string', 'max:1000'],
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        /** @var ProductReview $review */
        $review = $product->reviews()->create($validated);

        $product = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($product->id);

        return response()->json([
            'message' => 'Merci, votre avis a ete ajoute.',
            'metrics' => [
                'reviewCount' => (int) ($product->reviews_count ?? 0),
                'averageRating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
            ],
            'review' => [
                'author_name' => $review->author_name,
                'body' => $review->body,
                'rating' => (int) $review->rating,
                'created_at' => $review->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Build a product payload for frontend updates.
     */
    private function productState(Request $request, Product $product): array
    {
        return [
            'slug' => $product->slug,
            'code' => $product->code,
            'name' => $product->name,
            'preorderCount' => (int) $product->preorder_count,
            'hasPreordered' => $this->hasEffectivePreorder($request, $product),
            'detailsUrl' => route('products.show', $product),
        ];
    }

    /**
     * Determine whether the current visitor still has a valid preorder.
     */
    private function hasEffectivePreorder(Request $request, Product $product): bool
    {
        return $this->interactionTracker->hasPreorder($request, $product);
    }

    /**
     * Build a WhatsApp URL for a product preorder.
     */
    private function whatsAppUrlForProduct(Product $product): ?string
    {
        $number = preg_replace(
            '/\D+/',
            '',
            (string) SiteSetting::value('whatsapp_number', (string) config('services.whatsapp.number')),
        );

        if ($number === '') {
            return null;
        }

        // Convert Beninese format (starting with 0) to international format (229)
        if (str_starts_with($number, '0')) {
            $number = '229' . substr($number, 1);
        }

        $message = sprintf(
                'Bonjour, je souhaite precommander %s sur King Rangement Benin. Voici le produit : %s',
            $product->name,
            route('products.show', $product),
        );

        return sprintf('https://wa.me/%s?text=%s', $number, rawurlencode($message));
    }
}

