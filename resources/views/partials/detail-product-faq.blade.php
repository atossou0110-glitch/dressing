@props([
    'heading' => 'Questions frequentes sur ce produit',
    'description' => 'Les derniers doutes qui restent souvent avant la validation.',
    'items' => [],
])

@if (!empty($items))
    <section class="brand-section-light py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 max-w-3xl">
                <p class="brand-kicker brand-kicker-light">FAQ produit</p>
                <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $heading }}</h2>
                <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">{{ $description }}</p>
            </div>

            <div class="storefront-fixed-grid-2 grid gap-4 lg:grid-cols-2">
                @foreach ($items as $item)
                    <details class="group brand-faq-item p-5" data-reveal>
                        <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                            <span>{{ $item['question'] }}</span>
                            <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                        </summary>
                        <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                            {{ $item['answer'] }}
                        </p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif
