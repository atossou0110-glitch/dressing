@props([
    'heading' => 'Comment choisir sans se tromper',
    'description' => 'Une methode simple pour transformer une visite catalogue en vrai projet meuble.',
    'steps' => [],
])

<section class="brand-section-light py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 max-w-3xl">
            <p class="brand-kicker brand-kicker-light">Methode</p>
            <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $heading }}</h2>
            <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">{{ $description }}</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($steps as $step)
                <article class="brand-card flex h-full flex-col p-6 shadow-[0_18px_45px_rgba(2,25,31,0.08)]" data-reveal>
                    <div class="flex items-center justify-between gap-4">
                        <span class="brand-tool-icon text-base font-semibold uppercase tracking-[0.18em]">{{ $step['step'] }}</span>
                        <span class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--brand-sand-dark)]">Etape</span>
                    </div>

                    <h3 class="mt-5 text-2xl font-semibold text-[var(--brand-ink)]">{{ $step['title'] }}</h3>
                    <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">{{ $step['description'] }}</p>

                    @if (!empty($step['points']))
                        <ul class="mt-5 space-y-3 text-sm leading-7 text-[var(--brand-copy)]">
                            @foreach ($step['points'] as $point)
                                <li class="flex items-start gap-3">
                                    <span class="mt-2 inline-block h-2 w-2 rounded-full bg-[var(--brand-deep)]"></span>
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
