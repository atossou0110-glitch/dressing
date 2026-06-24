<?php

namespace App\Http\Controllers;

use App\Models\BrowserNotificationSubscription;
use App\Models\FlashCampaign;
use App\Support\ProductInteractionTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlashNotificationController extends Controller
{
    public function __construct(
        private readonly ProductInteractionTracker $interactionTracker,
    ) {}

    /**
     * Save the browser notification consent state for the current visitor.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'permission' => ['required', 'in:default,granted,denied'],
            'email' => ['nullable', 'email', 'max:255'],
            'source_path' => ['nullable', 'string', 'max:255'],
        ]);

        $visitorHash = $this->interactionTracker->visitorHash($request);
        $existing = BrowserNotificationSubscription::query()
            ->where('visitor_hash', $visitorHash)
            ->first();

        $meta = is_array($existing?->meta) ? $existing->meta : [];
        $meta['source_path'] = trim((string) ($validated['source_path'] ?? $request->path()));
        $meta['user_agent'] = substr((string) $request->userAgent(), 0, 255);

        $subscription = BrowserNotificationSubscription::query()->updateOrCreate(
            ['visitor_hash' => $visitorHash],
            [
                'email' => $validated['email'] ?? $existing?->email,
                'permission' => $validated['permission'],
                'subscribed_at' => $validated['permission'] === 'granted'
                    ? ($existing?->subscribed_at ?? now())
                    : $existing?->subscribed_at,
                'last_seen_at' => now(),
                'meta' => $meta,
            ],
        );

        return response()->json([
            'success' => true,
            'permission' => $subscription->permission,
        ]);
    }

    /**
     * Return the latest active flash campaign for the current visitor.
     */
    public function latest(Request $request): JsonResponse
    {
        $visitorHash = $this->interactionTracker->visitorHash($request);

        $subscription = BrowserNotificationSubscription::query()->firstOrCreate(
            ['visitor_hash' => $visitorHash],
            [
                'permission' => 'default',
                'last_seen_at' => now(),
            ],
        );

        $subscription->forceFill([
            'last_seen_at' => now(),
        ])->save();

        $campaign = FlashCampaign::query()
            ->activeNow()
            ->latest('starts_at')
            ->latest('id')
            ->first();

        if ($campaign === null) {
            return response()->json([
                'campaign' => null,
                'shouldNotify' => false,
            ]);
        }

        $shouldNotify = $subscription->permission === 'granted'
            && $subscription->last_notified_campaign_id !== $campaign->id;

        if ($shouldNotify) {
            $subscription->forceFill([
                'last_notified_campaign_id' => $campaign->id,
                'last_notified_at' => now(),
            ])->save();

            $campaign->increment('impressions_count');
        }

        return response()->json([
            'shouldNotify' => $shouldNotify,
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'message' => $campaign->message,
                'discountCode' => $campaign->discount_code,
                'ctaLabel' => $campaign->cta_label ?: 'Voir l offre',
                'ctaUrl' => $campaign->cta_url ?: route('catalog.index'),
                'endsAt' => $campaign->ends_at?->toIso8601String(),
            ],
        ]);
    }
}
