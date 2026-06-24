@props([
    'detailAdvice' => [
        'heading' => 'Bien choisir ce meuble',
        'description' => 'Quelques reperes concrets pour verifier que le produit correspond vraiment a votre espace.',
        'cards' => [],
    ],
])

<section class="brand-section-light py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 max-w-3xl">
            <p class="brand-kicker brand-kicker-light">Avant achat</p>
            <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $detailAdvice['heading'] }}</h2>
            <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">{{ $detailAdvice['description'] }}</p>
        </div>

        <div class="storefront-fixed-grid-3 grid gap-6 lg:grid-cols-3">
            @foreach ($detailAdvice['cards'] as $card)
                <article class="brand-card flex h-full flex-col p-6 shadow-[0_18px_45px_rgba(2,25,31,0.08)]" data-reveal>
                    <h3 class="text-2xl font-semibold text-[var(--brand-ink)]">{{ $card['title'] }}</h3>
                    <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">{{ $card['description'] }}</p>

                    @if (!empty($card['points']))
                        <ul class="mt-5 space-y-3 text-sm leading-7 text-[var(--brand-copy)]">
                            @foreach ($card['points'] as $point)
                                <li class="flex items-start gap-3">
                                    <span class="mt-2 inline-block h-2 w-2 rounded-full bg-[var(--brand-sand-dark)]"></span>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
