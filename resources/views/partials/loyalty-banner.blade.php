@props(['summary' => null])

<section class="brand-section-muted py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if ($summary)
            <div class="storefront-fixed-loyalty-grid grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <article class="brand-guide-card p-6">
                    <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F3C5;</span> Fidélité</p>
                    <h2 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">
                        {{ $summary['points'] }} points - niveau {{ $summary['tierLabel'] }}
                    </h2>
                    <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                        Chaque achat valide ajoute des points a votre compte et fait progresser votre niveau fidelite.
                    </p>
                    @if ($summary['nextTierLabel'])
                        <p class="mt-4 text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-sand-dark)]">
                            Encore {{ $summary['pointsToNextTier'] }} points pour atteindre {{ $summary['nextTierLabel'] }}.
                        </p>
                    @endif
                </article>

                <div class="storefront-fixed-grid-3 grid gap-4 sm:grid-cols-2">
                    <article class="brand-info-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand-teal-soft)]">Points</p>
                        <p class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">{{ $summary['points'] }}</p>
                    </article>

                    <article class="brand-info-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand-teal-soft)]">Niveau</p>
                        <p class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">{{ $summary['tierLabel'] }}</p>
                    </article>
                </div>
            </div>
        @endif
    </div>
</section>

