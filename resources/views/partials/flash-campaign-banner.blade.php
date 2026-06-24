@props(['campaign' => null])

@if ($campaign)
    <section class="brand-section-light pb-0 pt-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="brand-filter-panel storefront-fixed-row storefront-fixed-row-between storefront-fixed-align-center flex flex-col gap-5 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <p class="brand-kicker brand-kicker-light">Reduction flash</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">{{ $campaign->title }}</h2>
                    <p class="mt-3 text-sm leading-7 text-[var(--brand-copy)]">{{ $campaign->message }}</p>

                    @if ($campaign->ends_at)
                        <p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-[var(--brand-sand-dark)]">
                            Offre active jusqu au {{ $campaign->ends_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($campaign->discount_code)
                        <span class="brand-filter-result">Code {{ $campaign->discount_code }}</span>
                    @endif

                    <a href="{{ $campaign->cta_url ?: route('catalog.index') }}" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                        {{ $campaign->cta_label ?: 'Voir l offre' }}
                    </a>

                    <button
                        type="button"
                        data-notification-enable
                        data-notification-subscribe-url="{{ route('notifications.subscribe') }}"
                        data-notification-latest-url="{{ route('notifications.latest') }}"
                        data-notification-email="{{ auth()->user()?->email }}"
                        class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]"
                    >
                        Activer les alertes flash
                    </button>
                </div>
            </div>
        </div>
    </section>
@endif
