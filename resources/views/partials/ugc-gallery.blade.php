@props([
    'items' => collect(),
    'heading' => 'Galerie client',
    'description' => 'Des photos envoyees par les clients pour montrer le meuble en situation reelle.',
    'sourceProductSlug' => null,
])

@php
    $items = collect($items);
@endphp

<section class="brand-section-light py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="storefront-fixed-ugc-grid grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
            <div>
                <p class="brand-kicker brand-kicker-light">UGC</p>
                <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $heading }}</h2>
                <p class="mt-4 max-w-3xl text-base leading-7 text-[var(--brand-copy)]">{{ $description }}</p>

                <div class="storefront-fixed-grid-3 mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($items as $item)
                        <article class="brand-card overflow-hidden">
                            <img src="{{ $item->photoUrl() }}" alt="{{ $item->caption }}" class="aspect-[4/5] w-full object-cover object-center">
                            <div class="p-4">
                                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-sand-dark)]">{{ $item->author_name }}</p>
                                <p class="mt-2 text-sm leading-7 text-[var(--brand-copy)]">{{ $item->caption }}</p>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.12em] text-[var(--brand-teal-soft)]">
                                    @if ($item->author_city)
                                        <span>{{ $item->author_city }}</span>
                                    @endif
                                    @if ($item->product)
                                        <a href="{{ route('products.show', $item->product) }}" class="transition hover:text-[var(--brand-deep)]">
                                            {{ $item->product->name }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="brand-empty-state p-6 text-sm leading-7 text-[var(--brand-copy)] sm:col-span-2 lg:col-span-3">
                            Les premiers visuels clients apparaîtront ici après validation.
                        </article>
                    @endforelse
                </div>
            </div>

            <div class="brand-card p-6 shadow-[0_18px_45px_rgba(2,25,31,0.1)]">
                <p class="brand-kicker brand-kicker-light">Partager une photo</p>
                <h3 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Montrez votre intérieur</h3>
                <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                    Envoyez une photo de votre meuble installé chez vous. Après validation, elle pourra apparaître dans la galerie client du site.
                </p>

                <form data-ugc-form action="{{ route('ugc.submit') }}" class="mt-8 space-y-4" enctype="multipart/form-data">
                    <div data-ugc-feedback class="hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

                    <div>
                        <label for="ugc-name" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                            Votre nom
                        </label>
                        <input id="ugc-name" type="text" name="author_name" required class="brand-input w-full px-4 py-3 text-sm" placeholder="Votre nom">
                    </div>

                    <div class="storefront-fixed-grid-2 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="ugc-city" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                                Ville
                            </label>
                            <input id="ugc-city" type="text" name="author_city" class="brand-input w-full px-4 py-3 text-sm" placeholder="Cotonou">
                        </div>

                        <div>
                            <label for="ugc-email" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                                Email
                            </label>
                            <input id="ugc-email" type="email" name="author_email" class="brand-input w-full px-4 py-3 text-sm" placeholder="vous@exemple.com">
                        </div>
                    </div>

                    <div>
                        <label for="ugc-caption" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                            Legende
                        </label>
                        <textarea id="ugc-caption" name="caption" rows="4" required class="brand-input w-full px-4 py-3 text-sm" placeholder="Expliquez comment le meuble s integre chez vous."></textarea>
                    </div>

                    <div>
                        <label for="ugc-photo" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                            Photo
                        </label>
                        <input id="ugc-photo" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" required class="brand-input w-full px-4 py-3 text-sm">
                    </div>

                    @if ($sourceProductSlug)
                        <input type="hidden" name="source_product_slug" value="{{ $sourceProductSlug }}">
                    @endif

                    <button type="submit" class="brand-button-primary inline-flex w-full items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                        Envoyer ma photo
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

