@props(['reviews' => null])

@php
    // Filter to show only best reviews (5 and 4 stars)
    $topReviews = collect($reviews ?? [])
        ->filter(fn ($r) => ($r->rating ?? 0) >= 4)
        ->take(6)
        ->values();
        
    $hasReviews = $topReviews->count() > 0;
@endphp

<section id="avis-clients" class="brand-section-dark py-12 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <p class="brand-kicker">Témoignages clients</p>
            <h2 class="brand-display mt-3 text-3xl text-white sm:text-4xl">Vos avis nous font progresser</h2>
            <p class="mt-4 max-w-3xl text-base leading-7 text-[var(--brand-sand-soft)]">
                Découvrez ce que nos clients pensent de nos meubles et services. Vos retours nous aident à nous améliorer continuellement.
            </p>
        </div>

        @if ($hasReviews)
            <!-- Reviews Grid -->
            <div class="storefront-fixed-grid-3 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($topReviews as $review)
                    <article class="brand-dark-panel p-6 transition hover:bg-white/10" data-reveal>
                        <!-- Header with Name and Rating -->
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[var(--brand-sand)]">
                                    {{ $review->product?->name ?? 'Produit' }}
                                </p>
                                <h3 class="mt-2 text-xl font-semibold text-white">
                                    {{ $review->author_name }}
                                </h3>
                            </div>
                            <!-- Rating Stars -->
                            <div class="text-sm text-[var(--brand-sand)] flex-shrink-0">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= (int) $review->rating)
                                        <span>★</span>
                                    @else
                                        <span>☆</span>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        <!-- Review Title (if available) -->
                        @if (!empty($review->title))
                            <p class="mt-3 font-semibold text-[var(--brand-sand)]">
                                {{ $review->title }}
                            </p>
                        @endif

                        <!-- Review Body -->
                        <p class="mt-4 text-sm leading-7 text-[var(--brand-sand-soft)]">
                            "{{ \Illuminate\Support\Str::limit((string) $review->body, 200) }}"
                        </p>

                        <!-- Date -->
                        <p class="mt-5 text-xs font-semibold uppercase tracking-[0.14em] text-[rgba(214,194,169,0.42)]">
                            {{ optional($review->created_at)->format('d M Y') }}
                        </p>

                        <!-- Helpful? (Interaction) -->
                        <div class="mt-4 pt-4 border-t border-white/10 flex gap-3 text-xs">
                            <button class="text-[var(--brand-sand)] hover:text-white transition">
                                👍 Utile
                            </button>
                            <button class="text-[var(--brand-sand)] hover:text-white transition">
                                👎 Pas utile
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Call to Action for Reviews -->
            <div class="mt-10 text-center">
                <p class="text-[var(--brand-sand-soft)] mb-4">
                    Vous avez acheté l'un de nos produits? Partagez votre avis!
                </p>
                <a href="{{ route('catalog.index') }}#avis-clients" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Laisser un avis
                </a>
            </div>
        @else
            <!-- Empty State -->
            <div class="brand-dark-panel p-12 text-center">
                <div class="text-4xl mb-4">⭐</div>
                <p class="text-lg text-[var(--brand-sand-soft)]">
                    Aucun commentaire pour le moment
                </p>
                <p class="mt-2 text-sm text-[var(--brand-sand-soft)]">
                    Les premiers avis apparaîtront ici. Soyez le premier à partager votre expérience!
                </p>
                <a href="{{ route('catalog.index') }}" class="brand-button inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold uppercase tracking-[0.14em] mt-6 mx-auto">
                    Découvrir les produits
                </a>
            </div>
        @endif
    </div>
</section>
