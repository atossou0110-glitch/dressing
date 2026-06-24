<?php

namespace App\Support;

use App\Models\NewsletterConversion;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NewsletterAnalyticsService
{
    public function __construct(
        private readonly ProductInteractionTracker $interactionTracker,
    ) {}

    /**
     * Persist or refresh one newsletter subscription.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function subscribe(Request $request, array $attributes): NewsletterSubscription
    {
        $email = $this->normalizeEmail((string) ($attributes['email'] ?? ''));
        $product = $this->resolveProduct($attributes['source_product_slug'] ?? null);
        $existing = NewsletterSubscription::query()->where('email', $email)->first();
        $meta = is_array($existing?->meta) ? $existing->meta : [];

        $meta['user_agent'] = substr((string) $request->userAgent(), 0, 255);
        $meta['last_ip_hash'] = $this->hashIp($request);

        $subscription = NewsletterSubscription::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $this->stringOrNull($attributes['name'] ?? null),
                'visitor_hash' => $this->interactionTracker->visitorHash($request),
                'source_product_id' => $product?->id,
                'source_page' => $this->resolveSourcePage((string) ($attributes['source_path'] ?? $request->path())),
                'source_path' => $this->stringOrNull($attributes['source_path'] ?? $request->path()),
                'discount_code' => (string) ($existing?->discount_code ?: 'WELCOME10'),
                'status' => 'subscribed',
                'subscribed_at' => $existing?->subscribed_at ?? now(),
                'marketing_opt_in_at' => $existing?->marketing_opt_in_at ?? now(),
                'last_seen_at' => now(),
                'meta' => $meta,
            ],
        );

        return $subscription->fresh(['sourceProduct']) ?? $subscription;
    }

    /**
     * Attribute one approved order to an existing newsletter subscriber.
     */
    public function trackApprovedOrder(ProductOrder $order): ?NewsletterConversion
    {
        if (! $order->isPaid()) {
            return null;
        }

        $email = $this->normalizeEmail((string) $order->customer_email);

        if ($email === '') {
            return null;
        }

        $payload = is_array($order->provider_payload) ? $order->provider_payload : [];

        if (Arr::get($payload, 'growth.newsletter_conversion_recorded_at')) {
            return NewsletterConversion::query()
                ->where('product_order_id', $order->id)
                ->first();
        }

        $subscription = NewsletterSubscription::query()->where('email', $email)->first();

        if ($subscription === null) {
            return null;
        }

        $conversion = NewsletterConversion::query()->firstOrCreate(
            ['product_order_id' => $order->id],
            [
                'newsletter_subscription_id' => $subscription->id,
                'product_id' => $order->product_id,
                'amount' => (int) $order->amount,
                'status' => $order->status,
                'converted_at' => $order->paid_at ?? now(),
            ],
        );

        $subscription->forceFill([
            'conversion_orders_count' => NewsletterConversion::query()
                ->where('newsletter_subscription_id', $subscription->id)
                ->count(),
            'converted_revenue_total' => (int) NewsletterConversion::query()
                ->where('newsletter_subscription_id', $subscription->id)
                ->sum('amount'),
            'last_converted_at' => $conversion->converted_at,
            'last_seen_at' => now(),
        ])->save();

        $payload['growth']['newsletter_conversion_recorded_at'] = now()->toIso8601String();
        $payload['growth']['newsletter_subscription_id'] = $subscription->id;

        $order->forceFill([
            'provider_payload' => $payload,
        ])->save();

        return $conversion;
    }

    /**
     * Summarize newsletter performance for the admin dashboard.
     *
     * @return array{
     *     subscribers: int,
     *     recentSubscribers: int,
     *     conversions: int,
     *     revenue: int,
     *     conversionRate: float,
     *     topSources: list<array{label: string, total: int}>
     * }
     */
    public function overview(): array
    {
        $subscribers = NewsletterSubscription::query()->count();
        $recentSubscribers = NewsletterSubscription::query()
            ->where('subscribed_at', '>=', now()->subDays(7))
            ->count();
        $conversions = NewsletterConversion::query()->count();
        $revenue = (int) NewsletterConversion::query()->sum('amount');
        $sourceCounts = NewsletterSubscription::query()
            ->selectRaw('COALESCE(source_page, ?) as source_page, COUNT(*) as aggregate', ['site'])
            ->groupBy('source_page')
            ->orderByDesc('aggregate')
            ->limit(4)
            ->get();

        return [
            'subscribers' => $subscribers,
            'recentSubscribers' => $recentSubscribers,
            'conversions' => $conversions,
            'revenue' => $revenue,
            'conversionRate' => $subscribers > 0
                ? round(($conversions / $subscribers) * 100, 1)
                : 0.0,
            'topSources' => $sourceCounts
                ->map(fn ($row) => [
                    'label' => ucfirst(str_replace('-', ' ', (string) $row->source_page)),
                    'total' => (int) $row->aggregate,
                ])
                ->all(),
        ];
    }

    /**
     * Resolve a product from a slug-like source identifier.
     */
    private function resolveProduct(mixed $slug): ?Product
    {
        $candidate = trim((string) $slug);

        if ($candidate === '') {
            return null;
        }

        return Product::query()->where('slug', $candidate)->first();
    }

    /**
     * Normalize an email address for matching.
     */
    private function normalizeEmail(string $value): string
    {
        return Str::lower(trim($value));
    }

    /**
     * Resolve a small, stable source page label.
     */
    private function resolveSourcePage(string $path): string
    {
        $value = trim($path, '/');

        return match (true) {
            $value === '', $value === 'catalog' => 'homepage',
            str_starts_with($value, 'produit/') => 'product',
            str_starts_with($value, 'commandes/') => 'order',
            default => 'site',
        };
    }

    /**
     * Normalize optional strings.
     */
    private function stringOrNull(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    /**
     * Hash the current IP address for analytics.
     */
    private function hashIp(Request $request): ?string
    {
        $ip = trim((string) $request->ip());

        return $ip !== '' ? hash('sha256', $ip) : null;
    }
}
