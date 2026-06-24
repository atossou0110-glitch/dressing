<?php

namespace App\Http\Controllers;

use App\Support\NewsletterAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function __construct(
        private readonly NewsletterAnalyticsService $analytics,
    ) {}

    /**
     * Persist a newsletter subscription from the storefront.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:100'],
            'agree' => ['required', 'accepted'],
            'source_path' => ['nullable', 'string', 'max:255'],
            'source_product_slug' => ['nullable', 'string', 'max:120'],
        ]);

        $subscription = $this->analytics->subscribe($request, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Inscription newsletter enregistree.',
            'code' => $subscription->discount_code,
            'subscription' => [
                'email' => $subscription->email,
                'sourcePage' => $subscription->source_page,
                'sourceProduct' => $subscription->sourceProduct?->name,
            ],
        ], $subscription->wasRecentlyCreated ? 201 : 200);
    }
}
