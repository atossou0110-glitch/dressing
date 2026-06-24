@props([
    'story' => [
        'heading' => 'A qui ce meuble correspond le mieux',
        'description' => 'Des cas d usage concrets pour mieux se projeter avant achat.',
        'idealFor' => [],
        'benefits' => [],
        'useCases' => [],
    ],
])

<section class="brand-section-muted py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[0.92fr_1.08fr]">
            <div class="brand-card p-6 shadow-[0_18px_45px_rgba(2,25,31,0.08)]" data-reveal>
                <p class="brand-kicker brand-kicker-light">Ideal pour</p>
                <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $story['heading'] }}</h2>
                <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">{{ $story['description'] }}</p>

                @if (!empty($story['idealFor']))
                    <div class="mt-6 flex flex-wrap gap-3">
                        @foreach ($story['idealFor'] as $item)
                            <span class="brand-pill border border-[var(--brand-line)] bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-[var(--brand-ink)]">
                                {{ $item }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if (!empty($story['benefits']))
                    <div class="mt-8 space-y-4">
                        @foreach ($story['benefits'] as $benefit)
                            <div class="rounded-[1.4rem] border border-[var(--brand-line)] bg-[rgba(255,247,239,0.72)] px-4 py-4 text-sm leading-7 text-[var(--brand-copy)]">
                                {{ $benefit }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                @foreach ($story['useCases'] as $useCase)
                    <article class="brand-guide-card flex h-full flex-col p-6" data-reveal>
                        <p class="brand-kicker brand-kicker-light">Cas d usage</p>
                        <h3 class="mt-4 text-2xl font-semibold text-[var(--brand-ink)]">{{ $useCase['title'] }}</h3>
                        <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">{{ $useCase['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
