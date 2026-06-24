@props([
    'heading' => 'Pourquoi le catalogue parait plus complet',
    'description' => 'Des contenus de reassurance et de projection qui vont plus loin que la simple carte produit.',
    'items' => [],
])

<section class="brand-section-muted py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 max-w-3xl">
            <p class="brand-kicker brand-kicker-light">Parcours</p>
            <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $heading }}</h2>
            <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">{{ $description }}</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($items as $item)
                <article class="brand-info-panel h-full p-6" data-reveal>
                    @if (!empty($item['eyebrow']))
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand-teal-soft)]">{{ $item['eyebrow'] }}</p>
                    @endif
                    <h3 class="mt-4 text-2xl font-semibold text-[var(--brand-ink)]">{{ $item['title'] }}</h3>
                    <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">{{ $item['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
