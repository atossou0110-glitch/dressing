<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    @include('partials.fixed-viewport')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product['name'] }} | King Rangement Benin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="brand-body product-detail-body storefront-fixed-body min-h-screen antialiased">
    <header class="brand-header sticky top-0 z-50" data-mobile-header>
        <div class="mx-auto flex max-w-7xl flex-nowrap items-center justify-between gap-2 px-4 py-2 sm:px-6 lg:gap-2.5 lg:px-8">
            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    class="brand-mobile-menu-toggle inline-flex h-9 w-9 items-center justify-center rounded-md border border-[rgba(239,224,204,0.25)] text-[0.92rem] text-[var(--brand-sand-soft)] lg:hidden"
                    aria-controls="product-mobile-menu"
                    aria-expanded="false"
                    aria-label="Afficher le menu"
                    data-mobile-menu-toggle
                >
                    <span aria-hidden="true">&#9776;</span>
                </button>

                <a href="{{ route('catalog.index') }}" class="shrink-0">
                    <x-brand-logo class="brand-logo-header" />
                </a>
            </div>

            <nav class="hidden items-center gap-4 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-[var(--brand-teal-soft)] lg:flex">
                <a href="{{ route('catalog.index') }}" class="brand-nav-link">Accueil</a>
                <a href="{{ route('catalog.dr-dressing') }}" class="brand-nav-link">Solutions King</a>
                <a href="#commentaires" class="brand-nav-link">Commentaires</a>
                <a href="#suggestions" class="brand-nav-link">Suggestions</a>
            </nav>

            <a href="{{ route('catalog.index') }}" class="brand-header-button shrink-0 px-2.5 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em]">
                Retour
            </a>
        </div>

        <div id="product-mobile-menu" class="brand-mobile-menu lg:hidden" data-mobile-menu hidden>
            <div class="mx-auto grid max-w-7xl gap-2 px-4 pb-4 pt-3 sm:px-6">
                <a href="{{ route('catalog.index') }}" class="brand-mobile-menu-link">Accueil</a>
                <a href="{{ route('catalog.dr-dressing') }}" class="brand-mobile-menu-link">Solutions King</a>
                <a href="#commentaires" class="brand-mobile-menu-link">Commentaires</a>
                <a href="#suggestions" class="brand-mobile-menu-link">Suggestions</a>
                <a href="{{ route('catalog.index') }}" class="brand-mobile-menu-link">Retour</a>
            </div>
        </div>
    </header>

    <div class="storefront-fixed-frame" data-storefront-fixed-frame>
    <div class="storefront-fixed-stage" data-storefront-fixed-stage data-storefront-stage-width="1280">
    <main id="productDetailPage" class="brand-page-enter">
        <section class="brand-breadcrumb border-b border-[rgba(211,176,130,0.18)]">
            <div class="mx-auto max-w-7xl px-4 py-6 text-sm text-[var(--brand-sand-soft)] sm:px-6 lg:px-8">
                <a href="{{ route('catalog.index') }}" class="transition hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <span>{{ $collectionLabel }}</span>
                <span class="mx-2">/</span>
                <span>{{ $categoryLabel }}</span>
                <span class="mx-2">/</span>
                <span class="text-[var(--brand-sand)]">{{ $product['name'] }}</span>
            </div>
        </section>

        <section class="brand-detail-hero">
            <div class="storefront-fixed-product-hero-grid mx-auto grid max-w-7xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[minmax(0,36rem)_minmax(0,28rem)] lg:justify-center lg:gap-5 lg:px-8 lg:py-10">
                <div class="brand-detail-gallery-shell mx-auto w-full max-w-[36rem] space-y-4 lg:mx-0">
                    <div data-reveal="left">
                        <div
                            class="brand-photo-frame brand-detail-photo-frame relative overflow-hidden"
                            data-detail-carousel
                            aria-label="Photos du produit"
                            aria-roledescription="carousel"
                        >
                            <img
                                id="detailImage"
                                src="{{ $productGallery[0] ?? $product['imageUrl'] }}"
                                alt="{{ $product['name'] }}"
                                loading="eager"
                                class="brand-detail-image-fit block h-full w-full object-contain object-center"
                            >
                            @if (count($productGallery) > 1)
                                <button
                                    type="button"
                                    class="brand-detail-carousel-button brand-detail-carousel-button-prev"
                                    aria-label="Photo precedente"
                                    data-detail-prev
                                >
                                    <span aria-hidden="true">&#8249;</span>
                                </button>
                                <button
                                    type="button"
                                    class="brand-detail-carousel-button brand-detail-carousel-button-next"
                                    aria-label="Photo suivante"
                                    data-detail-next
                                >
                                    <span aria-hidden="true">&#8250;</span>
                                </button>
                                <p id="detailCarouselStatus" class="sr-only" aria-live="polite"></p>
                            @endif
                        </div>
                    </div>

                    <div class="storefront-fixed-grid-4 mx-auto grid max-w-[15rem] grid-cols-3 gap-2 sm:grid-cols-4">
                        @foreach ($productGallery as $index => $image)
                            <button
                                type="button"
                                data-detail-image="{{ $index }}"
                                class="brand-thumb {{ $index === 0 ? 'is-active' : '' }} overflow-hidden transition"
                                aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                            >
                                <span class="sr-only">Afficher la photo {{ $index + 1 }}</span>
                                <img src="{{ $image }}" alt="Photo {{ $index + 1 }} de {{ $product['name'] }}" class="brand-detail-thumb-fit aspect-square h-full w-full object-contain object-center p-1">
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mx-auto flex max-w-[28rem] flex-col justify-center" data-reveal="right">
                    <p class="brand-kicker">{{ $product['collectionLabel'] }} . {{ $product['categoryLabel'] }}</p>
                    @if (! empty($product['detailBadge']))
                        <p class="brand-detail-badge mt-3">{{ $product['detailBadge'] }}</p>
                    @endif
                    <h1 class="storefront-fixed-hero-display brand-display brand-product-title mt-3 text-white">
                        {{ $product['name'] }}
                    </h1>
                    <p class="brand-product-description mt-4 max-w-2xl text-[var(--brand-copy)]">
                        {{ $product['detailSubtitle'] }}
                    </p>
                    <p class="brand-product-description mt-3 max-w-2xl text-[var(--brand-copy)]">
                        Chaque detail est pense pour que vous vous sentiez mieux chez vous, avec moins d'encombrement, plus de place et une sensation immediate d'ordre.
                    </p>
                    @if (! empty($product['homeHighlight']))
                        <p class="brand-detail-highlight mt-4">{{ $product['homeHighlight'] }}</p>
                    @endif

                    <div class="storefront-fixed-grid-2 mt-6 grid gap-3 sm:grid-cols-2">
                        <article class="brand-metric-card p-3.5">
                            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-[var(--brand-copy)]"><span aria-hidden="true">&#x1F3F7;&#xFE0F;</span> Prix</p>
                            <p class="brand-product-price mt-1.5 text-white">{{ $product['homePrice'] }}</p>
                        </article>
                        <article class="brand-metric-card p-3.5">
                            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-[var(--brand-copy)]"><span aria-hidden="true">&#x2B50;</span> Avis</p>
                            <p class="mt-1.5 text-lg font-semibold text-white">
                                <span id="averageRating">{{ number_format($product['averageRating'], 1) }}</span>/5
                            </p>
                            <p class="mt-1 text-[0.68rem] text-[var(--brand-copy)]"><span id="ratingCount">{{ $product['reviewCount'] }}</span> commentaires</p>
                        </article>
                    </div>

                    <div class="brand-info-glass mt-6 p-4">
                        <div class="storefront-fixed-row storefront-fixed-wrap flex flex-col gap-2.5 sm:flex-row sm:flex-wrap">
                            <a href="{{ $product['checkoutUrl'] }}" class="brand-button-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-[0.72rem] font-semibold uppercase tracking-[0.12em]">
                                Acheter maintenant
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @include('partials.flash-campaign-banner', ['campaign' => $activeFlashCampaign ?? null])

        @include('partials.loyalty-banner', ['summary' => $loyaltySummary ?? null])

        <section class="brand-section-light py-12">
            <div class="storefront-fixed-product-split-grid mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1fr_0.9fr] lg:px-8">
                <div>
                    <button type="button" class="collapsible-header flex w-full items-center justify-between gap-4" data-target="description-content" data-expanded="false" aria-expanded="false" aria-controls="description-content">
                        <div>
                            <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4DD;</span> Description</p>
                            <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)]">Ce que montre cette fiche produit</h2>
                        </div>
                        <span class="collapsible-icon text-3xl text-[var(--brand-ink)] transition-transform">-</span>
                    </button>
                    <div id="description-content" class="collapsible-content collapsed mt-5">
                    <p class="mt-5 text-base leading-8 text-[var(--brand-copy)]">
                        {{ $product['detailDescription'] }}
                    </p>
                    @if (! empty($product['features']))
                        <ul class="brand-detail-features mt-6 grid gap-3 sm:grid-cols-3">
                            @foreach ($product['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                    </div>
                </div>

                <div class="brand-card p-6">
                    <button type="button" class="collapsible-header flex w-full items-center justify-between gap-4" data-target="specs-content" data-expanded="false" aria-expanded="false" aria-controls="specs-content">
                        <div>
                            <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4D0;</span> Caractéristiques</p>
                            <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Fiche technique</h2>
                        </div>
                        <span class="collapsible-icon text-3xl text-[var(--brand-ink)] transition-transform">-</span>
                    </button>
                    <div id="specs-content" class="collapsible-content collapsed mt-5">
                    <ul class="mt-8 space-y-4">
                        @foreach ($product['specifications'] as $specification)
                            <li class="border-b border-[var(--brand-line)] pb-4 text-sm leading-7 text-[var(--brand-copy)]">
                                {{ $specification }}
                            </li>
                        @endforeach
                    </ul>
                    </div>
                </div>
            </div>
        </section>

        @include('partials.detail-advice-grid', ['detailAdvice' => $detailAdvice])

        <section id="commentaires" class="brand-section-muted py-12">
            <div class="storefront-fixed-comments-grid mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.95fr_1.05fr] lg:px-8">
                <div class="brand-card p-6 shadow-[0_18px_45px_rgba(2,25,31,0.1)]">
                    <button type="button" class="collapsible-header flex w-full items-center justify-between gap-4" data-target="comment-form-content" data-expanded="false" aria-expanded="false" aria-controls="comment-form-content">
                        <div>
                            <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4AC;</span> Espace commentaire</p>
                            <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Laisser un commentaire</h2>
                        </div>
                        <span class="collapsible-icon text-3xl text-[var(--brand-ink)] transition-transform">-</span>
                    </button>
                    <div id="comment-form-content" class="collapsible-content collapsed mt-4">
                    <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                        Partagez votre avis sur le produit affiché sur cette page. Votre note met à jour la moyenne visible sur la fiche et aide d'autres clients à choisir les bonnes solutions de rangements.
                    </p>

                    <form id="commentForm" action="{{ $product['reviewUrl'] }}" method="POST" class="mt-8 space-y-5">
                        @csrf

                        <div id="commentSuccess" class="hidden border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            Merci, votre commentaire a bien été ajouté.
                        </div>

                        <div>
                            <label for="commentName" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                                Votre nom
                            </label>
                            <input
                                id="commentName"
                                name="author_name"
                                type="text"
                                required
                                class="brand-input w-full px-4 py-3 text-sm"
                                placeholder="Votre nom"
                            >
                        </div>

                        <div>
                            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">Votre note</p>
                            <div class="flex flex-wrap gap-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" data-star="{{ $i }}" class="brand-star border px-3.5 py-2.5 text-xl transition">
                                        &#9733;
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <label for="commentText" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                                Votre commentaire
                            </label>
                            <textarea
                                id="commentText"
                                name="body"
                                rows="5"
                                required
                                class="brand-input w-full px-4 py-3 text-sm"
                                placeholder="Dites ce que vous aimez sur ce produit, son style ou ses rangements."
                            ></textarea>
                        </div>

                        <button type="submit" class="brand-button-primary inline-flex w-full items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                            Envoyer le commentaire
                        </button>
                    </form>
                    </div>
                </div>

                <div>
                    <button type="button" class="collapsible-header flex w-full items-center justify-between gap-4" data-target="comments-list-content" data-expanded="false" aria-expanded="false" aria-controls="comments-list-content">
                        <div>
                            <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4AC;</span> Commentaires récents</p>
                            <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Avis déjà visibles</h2>
                        </div>
                        <span class="collapsible-icon text-3xl text-[var(--brand-ink)] transition-transform">-</span>
                    </button>
                    <div id="comments-list-content" class="collapsible-content collapsed mt-5">
                    <div id="commentList" class="mt-8 space-y-4">
                        @forelse ($reviews as $review)
                            <article class="brand-card p-5 shadow-[0_16px_40px_rgba(2,25,31,0.08)]">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-[var(--brand-ink)]">{{ $review->author_name }}</h3>
                                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                                            {{ optional($review->created_at)->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-[var(--brand-sand-dark)]">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= (int) $review->rating)
                                                <span>&#9733;</span>
                                            @else
                                                <span>&#9734;</span>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">{{ $review->body }}</p>
                            </article>
                        @empty
                            <article data-empty-reviews class="brand-card border-dashed p-5 text-sm leading-7 text-[var(--brand-teal-soft)]">
                                Aucun commentaire pour le moment. Soyez le premier à réagir sur cette fiche.
                            </article>
                        @endforelse
                    </div>
                    </div>
                </div>
            </div>
        </section>

        @include('partials.detail-product-faq', [
            'heading' => 'Questions fréquentes sur '.$product['name'],
            'description' => 'Des réponses claires pour terminer la projection avant de passer à l\'achat.',
            'items' => $detailStorytelling['faqs'] ?? [],
        ])

        <section id="suggestions" class="brand-section-light py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4CC;</span> Suggestion</p>
                    <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">
                        Produits du même univers {{ strtolower($collectionLabel) }}
                    </h2>
                    <p class="mt-4 max-w-3xl text-base leading-7 text-[var(--brand-copy)]">
                        Cette zone suggère des produits du même univers que celui affiché sur la photo principale, pour prolonger la visite et vous aider à trouver le meuble qui réglera vraiment votre manque de place.
                    </p>
                </div>

                <div class="storefront-fixed-grid-3 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($suggestedProducts as $suggestedProduct)
                        @include('partials.storefront-product-card', ['product' => $suggestedProduct])
                    @endforeach
                </div>
            </div>
        </section>

    </main>

    <footer class="brand-footer py-8">
        <div class="storefront-fixed-footer mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm text-[var(--brand-sand-soft)] sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="flex items-center gap-4">
                <x-brand-logo class="brand-logo-header" compact />
                <p>&copy; 2026 King Rangement Benin. Catalogue e-commerce avec commentaires et suggestions.</p>
            </div>
            <div class="flex flex-wrap gap-5">
                <a href="{{ route('catalog.index') }}" class="brand-nav-link">Accueil</a>
                <a href="{{ route('catalog.dr-dressing') }}" class="brand-nav-link">Solutions King</a>
            </div>
        </div>
    </footer>
    </div>
    </div>

    @include('partials.newsletter-popup', ['newsletterSourceProductSlug' => $product['slug']])

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.Dressingue?.initProductDetail?.({
                product: @json($product),
                images: @json(array_values($productGallery)),
            });

            // Collapsible sections
            const setCollapsibleState = (header, content, expanded) => {
                header.setAttribute('data-expanded', expanded ? 'true' : 'false');
                header.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                content.classList.toggle('collapsed', !expanded);

                const icon = header.querySelector('.collapsible-icon');
                if (icon) {
                    icon.textContent = expanded ? '-' : '+';
                }
            };

            const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
            collapsibleHeaders.forEach(header => {
                const targetId = header.getAttribute('data-target');
                const content = document.getElementById(targetId);

                if (!content) {
                    return;
                }

                setCollapsibleState(header, content, header.getAttribute('data-expanded') === 'true');

                header.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isExpanded = this.getAttribute('data-expanded') === 'true';
                    setCollapsibleState(this, content, !isExpanded);
                });
            });
        });
    </script>
</body>
</html>
