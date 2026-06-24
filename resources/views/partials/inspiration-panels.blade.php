@props([
    'heading' => 'Inspirations d\'aménagement',
    'description' => 'Des scenes completes pour aider a se projeter au dela de la fiche produit.',
    'panels' => [],
])

<section class="brand-section-muted py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 max-w-3xl">
            <p class="brand-kicker brand-kicker-light">Inspirations</p>
            <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $heading }}</h2>
            <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">{{ $description }}</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            @foreach ($panels as $panel)
                <article class="brand-guide-card flex h-full flex-col p-6" data-reveal>
                    <p class="brand-kicker brand-kicker-light">{{ $panel['tag'] }}</p>
                    <h3 class="mt-4 text-2xl font-semibold text-[var(--brand-ink)]">{{ $panel['title'] }}</h3>
                    <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                        {{ $panel['description'] }}
                    </p>

                    @if (!empty($panel['points']))
                        <ul class="mt-5 space-y-3 text-sm leading-7 text-[var(--brand-copy)]">
                            @foreach ($panel['points'] as $point)
                                <li class="flex items-start gap-3">
                                    <span class="mt-2 inline-block h-2 w-2 rounded-full bg-[var(--brand-sand-dark)]"></span>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if (!empty($panel['product']))
                        <div class="mt-6 border-t border-[var(--brand-line)] pt-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--brand-sand-dark)]">Produit a explorer</p>
                            <a href="{{ $panel['product']['detailsUrl'] }}" class="brand-guide-link mt-3 inline-flex text-sm font-semibold">
                                {{ $panel['product']['name'] }}
                            </a>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
