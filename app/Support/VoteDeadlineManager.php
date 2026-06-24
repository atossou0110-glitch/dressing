<?php

namespace App\Support;

use App\Models\SiteSetting;
use Carbon\CarbonImmutable;

class VoteDeadlineManager
{
    private const SETTING_KEY = 'vote_ends_at';

    /**
     * Resolve the current vote deadline, creating a default one on first use.
     */
    public function currentDeadline(): CarbonImmutable
    {
        $deadline = $this->deadline();

        if ($deadline instanceof CarbonImmutable) {
            return $deadline;
        }

        $defaultDeadline = now()
            ->addHours(7)
            ->addMinutes(18)
            ->addSeconds(42)
            ->toImmutable();

        SiteSetting::store(self::SETTING_KEY, $defaultDeadline->toIso8601String());

        return $defaultDeadline;
    }

    /**
     * Resolve the stored vote deadline.
     */
    public function deadline(): ?CarbonImmutable
    {
        return $this->parse(SiteSetting::value(self::SETTING_KEY));
    }

    /**
     * Determine whether the vote is currently closed.
     */
    public function isClosed(): bool
    {
        $deadline = $this->deadline();

        if (! $deadline instanceof CarbonImmutable) {
            return false;
        }

        return now()->toImmutable()->greaterThanOrEqualTo($deadline);
    }

    /**
     * Store a vote deadline from a dashboard datetime-local value.
     */
    public function storeFromDashboard(?string $value): ?CarbonImmutable
    {
        $rawValue = trim((string) $value);

        if ($rawValue === '') {
            SiteSetting::store(self::SETTING_KEY, null);

            return null;
        }

        $timezone = (string) config('app.timezone', 'UTC');
        $deadline = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $rawValue, $timezone);

        if (! $deadline instanceof CarbonImmutable) {
            return null;
        }

        $deadline = $deadline->seconds(0);

        SiteSetting::store(self::SETTING_KEY, $deadline->toIso8601String());

        return $deadline;
    }

    /**
     * Resolve the deadline formatted for datetime-local inputs.
     */
    public function dashboardValue(): ?string
    {
        $deadline = $this->deadline();

        if (! $deadline instanceof CarbonImmutable) {
            return null;
        }

        return $deadline
            ->setTimezone((string) config('app.timezone', 'UTC'))
            ->format('Y-m-d\TH:i');
    }

    /**
     * Parse and normalize an ISO-like date string.
     */
    private function parse(?string $value): ?CarbonImmutable
    {
        $rawValue = trim((string) $value);

        if ($rawValue === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($rawValue);
        } catch (\Throwable) {
            return null;
        }
    }
}
