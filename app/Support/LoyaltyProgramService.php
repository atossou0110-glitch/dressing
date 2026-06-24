<?php

namespace App\Support;

use App\Models\ProductOrder;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LoyaltyProgramService
{
    /**
     * Thresholds used to name loyalty tiers.
     *
     * @var array<string, int>
     */
    private const TIER_THRESHOLDS = [
        'starter' => 0,
        'silver' => 60,
        'gold' => 160,
        'icon' => 320,
    ];

    /**
     * Award loyalty points for one approved order.
     *
     * @return array{user: User, pointsEarned: int, tier: string}|null
     */
    public function rewardApprovedOrder(ProductOrder $order): ?array
    {
        if (! $order->isPaid()) {
            return null;
        }

        $email = $this->normalizeEmail((string) $order->customer_email);

        if ($email === '') {
            return null;
        }

        $payload = is_array($order->provider_payload) ? $order->provider_payload : [];

        if (Arr::get($payload, 'growth.loyalty_rewarded_at')) {
            return null;
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($user === null) {
            return null;
        }

        $pointsEarned = max(20, (int) floor(((int) $order->amount) / 10000));

        $user->forceFill([
            'loyalty_points' => (int) $user->loyalty_points + $pointsEarned,
            'loyalty_lifetime_spend' => (int) $user->loyalty_lifetime_spend + (int) $order->amount,
            'last_loyalty_rewarded_at' => now(),
        ]);

        $user->loyalty_tier = $this->resolveTier((int) $user->loyalty_points);
        $user->save();

        $paidOrderCount = ProductOrder::query()
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->whereIn('status', ['approved', 'transferred'])
            ->count();

        $payload['growth']['loyalty_rewarded_at'] = now()->toIso8601String();
        $payload['growth']['loyalty_points_earned'] = $pointsEarned;
        $payload['growth']['loyalty_tier'] = $user->loyalty_tier;
        $payload['growth']['loyalty_paid_orders'] = $paidOrderCount;

        $order->forceFill([
            'provider_payload' => $payload,
        ])->save();

        return [
            'user' => $user->fresh() ?? $user,
            'pointsEarned' => $pointsEarned,
            'tier' => (string) $user->loyalty_tier,
        ];
    }

    /**
     * Build a customer-facing loyalty snapshot.
     *
     * @return array{
     *     points: int,
     *     tier: string,
     *     tierLabel: string,
     *     nextTierLabel: string|null,
     *     pointsToNextTier: int|null
     * }|null
     */
    public function snapshotForUser(?User $user): ?array
    {
        if (! $user instanceof User) {
            return null;
        }

        $currentTier = (string) $user->loyalty_tier;
        $nextTier = $this->nextTier($currentTier);

        return [
            'points' => (int) $user->loyalty_points,
            'tier' => $currentTier,
            'tierLabel' => $this->labelForTier($currentTier),
            'nextTierLabel' => $nextTier !== null ? $this->labelForTier($nextTier) : null,
            'pointsToNextTier' => $nextTier !== null
                ? max(0, self::TIER_THRESHOLDS[$nextTier] - (int) $user->loyalty_points)
                : null,
        ];
    }

    /**
     * Build a leaderboard for the admin dashboard.
     *
     * @return list<array{name: string, email: string, points: int, tier: string}>
     */
    public function leaderboard(int $limit = 5): array
    {
        return User::query()
            ->orderByDesc('loyalty_points')
            ->limit($limit)
            ->get()
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'points' => (int) $user->loyalty_points,
                'tier' => $this->labelForTier((string) $user->loyalty_tier),
            ])
            ->all();
    }

    /**
     * Resolve the tier that matches a point balance.
     */
    private function resolveTier(int $points): string
    {
        $resolved = 'starter';

        foreach (self::TIER_THRESHOLDS as $tier => $threshold) {
            if ($points >= $threshold) {
                $resolved = $tier;
            }
        }

        return $resolved;
    }

    /**
     * Resolve the next loyalty tier after the current one.
     */
    private function nextTier(string $tier): ?string
    {
        $tiers = array_keys(self::TIER_THRESHOLDS);
        $currentIndex = array_search($tier, $tiers, true);

        if ($currentIndex === false || ! isset($tiers[$currentIndex + 1])) {
            return null;
        }

        return $tiers[$currentIndex + 1];
    }

    /**
     * Convert a tier code into a customer-friendly label.
     */
    private function labelForTier(string $tier): string
    {
        return match ($tier) {
            'silver' => 'Silver',
            'gold' => 'Gold',
            'icon' => 'Icon',
            default => 'Starter',
        };
    }

    /**
     * Normalize an email address for matching.
     */
    private function normalizeEmail(string $value): string
    {
        return Str::lower(trim($value));
    }
}
