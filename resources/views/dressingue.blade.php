@php
    $heroGalleryItems = collect($rangementProducts)
        ->pluck('imageUrl')
        ->merge(collect($dressingProducts)->pluck('imageUrl'))
        ->filter()
        ->values()
        ->all();

    // URL-encode and wrap fallback images with asset()
    $heroGalleryItems = collect($heroGalleryItems)
        ->map(fn ($url) => str_starts_with($url, '/images/') ? asset($url) : $url)
        ->all();

    if (count($heroGalleryItems) === 0) {
        $heroGalleryItems = [asset('images/commod.png')];
    }

    while (count($heroGalleryItems) < 8) {
        $heroGalleryItems = array_merge($heroGalleryItems, $heroGalleryItems);
    }

    $heroTopRowItems = array_slice($heroGalleryItems, 0, max(8, count($heroGalleryItems)));
    $heroBottomRowItems = array_reverse($heroTopRowItems);
    $bottomHeroVideoUrls = [
        asset('videos/showcase-main.mp4'),
        asset('videos/showcase-interior.mp4'),
        asset('videos/showcase-wardrobe.mp4'),
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    @include('partials.fixed-viewport')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>King Rangement Benin | Chaque chose a sa place</title>
    @foreach ($bottomHeroVideoUrls as $videoUrl)
        <link rel="preload" href="{{ $videoUrl }}" as="video" type="video/mp4">
    @endforeach
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="brand-body storefront-fixed-body min-h-screen antialiased">
    <header class="brand-header sticky top-0 z-50" data-mobile-header>
        <div class="mx-auto flex max-w-7xl flex-nowrap items-center justify-between gap-2 px-4 py-2 sm:px-6 lg:gap-2.5 lg:px-8">
            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    class="brand-mobile-menu-toggle inline-flex h-9 w-9 items-center justify-center rounded-md border border-[rgba(239,224,204,0.25)] text-[0.92rem] text-[var(--brand-sand-soft)] lg:hidden"
                    aria-controls="catalog-mobile-menu"
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

            <nav class="hidden items-center gap-4 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-[var(--brand-sand)] lg:flex">
                <a href="#accueil" class="brand-nav-link">Accueil</a>
                <a href="#rangement" class="brand-nav-link">Rangements</a>
                <a href="#dressing" class="brand-nav-link">Dressing</a>
            </nav>


            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('catalog.dr-dressing') }}" class="brand-header-button hidden px-2.5 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] md:inline-flex">
                    Solutions King
                </a>

                @auth
                    @if (Auth::user()->is_admin)
                        <a href="{{ route('dashboard') }}" class="brand-header-button hidden px-2.5 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] sm:inline-flex">
                            Dashboard
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="brand-header-button px-2.5 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em]">
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="brand-header-button hidden px-2.5 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] sm:inline-flex">
                        Connexion
                    </a>
                @endauth
            </div>
        </div>

        <div id="catalog-mobile-menu" class="brand-mobile-menu lg:hidden" data-mobile-menu hidden>
            <div class="mx-auto grid max-w-7xl gap-2 px-4 pb-4 pt-3 sm:px-6">
                <a href="#accueil" class="brand-mobile-menu-link">Accueil</a>
                <a href="#qui-sommes-nous" class="brand-mobile-menu-link">Qui sommes-nous</a>
                <a href="#rangement" class="brand-mobile-menu-link">Rangements</a>
                <a href="#dressing" class="brand-mobile-menu-link">Dressing</a>
                <a href="{{ route('catalog.dr-dressing') }}" class="brand-mobile-menu-link">Solutions King</a>
                @auth
                    @if (Auth::user()->is_admin)
                        <a href="{{ route('dashboard') }}" class="brand-mobile-menu-link">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="brand-mobile-menu-link w-full text-left">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="brand-mobile-menu-link">Connexion</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="storefront-fixed-frame" data-storefront-fixed-frame>
    <div class="storefront-fixed-stage" data-storefront-fixed-stage data-storefront-stage-width="1280">
    <main class="brand-page-enter">
        @include('partials.storefront-notices')

        <section id="accueil" class="brand-hero relative overflow-hidden">
            <div class="hero-background-layer" aria-hidden="true">
                <div class="hero-background-lane hero-background-lane-top">
                    <div class="hero-background-track" data-hero-marquee="forward" data-hero-speed="22">
                        @for ($sequence = 0; $sequence < 4; $sequence++)
                            <div class="hero-background-sequence">
                                @foreach ($heroTopRowItems as $image)
                                    <div class="hero-background-frame">
                                        <img src="{{ $image }}" alt="" class="hero-background-photo">
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="max-w-[42rem]">
                    <div class="brand-hero-copy-panel p-6 sm:p-7" data-reveal="left">
                        <p class="brand-kicker animate-text-reveal">Chaque chose a sa place</p>
                        <h1 class="storefront-fixed-hero-display brand-display mt-5 text-4xl leading-tight text-[var(--brand-sand)] sm:text-5xl animate-text-reveal" style="animation-delay: 0.2s;">
                            Acheter des meubles pour cacher le desordre ne resout rien.
                        </h1>
                        <p class="brand-home-hero-copy mt-5 max-w-2xl text-[var(--brand-sand-soft)] animate-stagger-in" style="animation-delay: 0.35s;">
                            King Rangement Benin repense votre maniere d'habiter avec des meubles optimises: commodes, etageres, armoires et dressings qui donnent une vraie place a chaque objet.
                        </p>

                        <div class="storefront-fixed-row storefront-fixed-wrap mt-7 flex flex-col gap-3 sm:flex-row">
                            <a href="#catalog-results" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                Voir nos solutions
                            </a>
                            <a href="#qui-sommes-nous" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                Qui sommes-nous ?
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        @include('partials.flash-campaign-banner', ['campaign' => $activeFlashCampaign ?? null])

        @include('partials.loyalty-banner', ['summary' => $loyaltySummary ?? null])

        <section id="qui-sommes-nous" class="brand-section-light py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="brand-intro-grid grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div data-reveal>
                        <p class="brand-kicker brand-kicker-light">Qui sommes-nous ?</p>
                        <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">
                            King Rangement Benin, le specialiste du mobilier optimise.
                        </h2>
                        <p class="mt-5 text-base leading-8 text-[var(--brand-copy)]">
                            Notre role n'est pas de vous vendre un meuble de plus. Notre role est de donner une place claire a vos chaussures, vetements, sacs, documents et objets du quotidien.
                        </p>
                    </div>

                    <div class="brand-manifest-grid grid gap-4 sm:grid-cols-3" data-reveal>
                        <article class="brand-manifest-item">
                            <span>Qui</span>
                            <p>Une equipe orientee rangement, mobilier pratique et optimisation des petits comme des grands espaces.</p>
                        </article>
                        <article class="brand-manifest-item">
                            <span>Pourquoi</span>
                            <p>Parce qu'un interieur apaise commence quand chaque chose retrouve enfin sa vraie place.</p>
                        </article>
                        <article class="brand-manifest-item">
                            <span>Comment</span>
                            <p>Avec des commodes, etageres, armoires et dressings choisis pour leur volume utile et leur usage quotidien.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>



        <div id="catalog-results" class="scroll-mt-28"></div>

        @if ($catalogFilters['hasActiveFilters'])
            <section class="brand-section-light pb-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="brand-filter-panel storefront-fixed-row storefront-fixed-row-between storefront-fixed-align-center flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div>
                            <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F50E;</span> Recherche</p>
                            <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Resultats du catalogue</h2>
                            <p class="mt-3 text-sm leading-7 text-[var(--brand-copy)]">
                                @if ($catalogFilters['q'] !== '')
                                    Résultats pour "{{ $catalogFilters['q'] }}".
                                @else
                                    Filtres actifs sur le catalogue.
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="brand-filter-result">{{ $catalogFilters['resultsCount'] }} produit(s) visible(s)</span>
                            <a href="{{ route('catalog.index') }}#catalog-results" class="brand-header-button inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if ($catalogFilters['resultsCount'] === 0)
            <section class="brand-section-light pb-10">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="brand-empty-state p-6 text-sm leading-7 text-[var(--brand-copy)]">
                        Aucun produit ne correspond à ces filtres pour l'instant. Élargissez la recherche ou réinitialisez les filtres pour revoir tout le catalogue.
                    </div>
                </div>
            </section>
        @endif

        @if ($catalogFilters['q'] !== '' && count($searchResults) > 0)
            <section class="brand-section-light pb-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="brand-search-spotlight p-6 sm:p-8" data-reveal>
                        <div class="storefront-fixed-row storefront-fixed-row-between storefront-fixed-align-end flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                            <div class="max-w-3xl">
                                <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F50E;</span> Recherche active</p>
                                <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">
                                    Resultats pour "{{ $catalogFilters['q'] }}"
                                </h2>
                                <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">
                                    Les produits trouvés sont regroupés ici en premier pour rendre la recherche visible immédiatement.
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <span class="brand-search-query">{{ $catalogFilters['q'] }}</span>
                                <span class="brand-filter-result">{{ $catalogFilters['resultsCount'] }} produit(s) trouvés</span>
                            </div>
                        </div>

                        <div class="brand-search-grid storefront-fixed-grid-3 mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($searchResults as $product)
                                @include('partials.storefront-product-card', ['product' => $product])
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section id="rangement" class="brand-section-light py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8" data-reveal>
                    <div>
                        <h2 class="brand-display text-3xl text-[var(--brand-ink)] sm:text-4xl"><span aria-hidden="true">&#x1F5C4;&#xFE0F;</span> Rangements</h2>
                    </div>
                </div>

                <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 md:gap-4">
                    <button
                        type="button"
                        data-track-prev="rangement-track"
                        aria-label="Défiler la collection rangements vers la gauche"
                        class="brand-track-button flex h-14 w-14 shrink-0 items-center justify-center px-0 py-0 text-4xl font-black leading-none md:h-16 md:w-16"
                    >
                        &larr;
                    </button>

                    <div class="min-w-0">
                        <div id="rangement-track" data-product-track class="product-track flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory">
                            @forelse ($rangementProducts as $product)
                                <div class="storefront-fixed-carousel-item min-w-0 snap-start flex-none basis-[88%] sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(33.333%-1rem)]">
                                    @include('partials.storefront-product-card', ['product' => $product])
                                </div>
                            @empty
                                <div class="brand-empty-state w-full p-6 text-sm leading-7 text-[var(--brand-copy)]">
                                    Aucun produit dans la collection Rangements ne correspond à la recherche actuelle.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <button
                        type="button"
                        data-track-next="rangement-track"
                        aria-label="Défiler la collection rangements vers la droite"
                        class="brand-track-button flex h-14 w-14 shrink-0 items-center justify-center px-0 py-0 text-4xl font-black leading-none md:h-16 md:w-16"
                    >
                        &rarr;
                    </button>
                </div>
            </div>
        </section>

        @php
            $videoFeatureProduct = collect($rangementProducts)->firstWhere('code', 'D')
                ?? ($rangementProducts[0] ?? null);
        @endphp

        @include('partials.hero-video-section', [
            'featuredProduct' => $videoFeatureProduct,
            'copy' => [
                'eyebrow' => 'À la une',
                'title' => 'Transformez votre espace de vie avec style et élégance',
                'description' => 'Découvrez notre collection de meubles de qualité pour créer l’intérieur de vos rêves. Des solutions modernes et intemporelles adaptées à tous les styles.',
                'chips' => ['Qualité premium', 'Livraison garantie'],
                'meta_label' => null,
                'meta_value' => null,
                'price' => null,
                'primary_cta_label' => null,
                'secondary_cta_label' => 'Solutions King',
                'clip_labels' => [
                    'Design élégant',
                    'Qualité supérieure',
                    'Style moderne',
                ],
            ],
        ])

        <section id="dressing" class="brand-section-muted py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="storefront-fixed-row storefront-fixed-row-between storefront-fixed-align-end mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div data-reveal>
                        <h2 class="brand-display text-3xl text-[var(--brand-ink)] sm:text-4xl"><span aria-hidden="true">&#x1F6AA;</span> Dressing</h2>
                    </div>

                    <div class="flex items-center gap-3">
                            <a href="{{ route('catalog.dr-dressing') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                            Explorer les solutions King
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 md:gap-4">
                    <button
                        type="button"
                        data-track-prev="dressing-track"
                        aria-label="Défiler la collection dressing vers la gauche"
                        class="brand-track-button flex h-14 w-14 shrink-0 items-center justify-center px-0 py-0 text-4xl font-black leading-none md:h-16 md:w-16"
                    >
                        &larr;
                    </button>

                    <div class="min-w-0">
                        <div id="dressing-track" data-product-track class="product-track flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory">
                            @forelse ($dressingProducts as $product)
                                <div class="storefront-fixed-carousel-item min-w-0 snap-start flex-none basis-[88%] sm:basis-[calc(50%-0.75rem)] lg:basis-[calc(33.333%-1rem)]">
                                    @include('partials.storefront-product-card', ['product' => $product])
                                </div>
                            @empty
                                <div class="brand-empty-state w-full p-6 text-sm leading-7 text-[var(--brand-copy)]">
                                    Aucun produit dressing ne correspond à la recherche actuelle.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <button
                        type="button"
                        data-track-next="dressing-track"
                        aria-label="Défiler la collection dressing vers la droite"
                        class="brand-track-button flex h-14 w-14 shrink-0 items-center justify-center px-0 py-0 text-4xl font-black leading-none md:h-16 md:w-16"
                    >
                        &rarr;
                    </button>
                </div>
            </div>
        </section>

        @include('partials.logistics-info')
    </main>

    <footer class="brand-footer py-8">
        @include('partials.social-links')
        
        <div class="storefront-fixed-footer mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm text-[var(--brand-sand-soft)] sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="flex items-center gap-4">
                <x-brand-logo class="brand-logo-header" compact />
                <p>&copy; 2026 King Rangement Benin. Catalogue meuble avec panier, filtres, commentaires et fiches produit enrichies.</p>
            </div>
            <div class="flex flex-wrap gap-5">
                <a href="#rangement" class="brand-nav-link">Rangements</a>
                <a href="#dressing" class="brand-nav-link">Dressing</a>
                <a href="{{ route('faq') }}" class="brand-nav-link">FAQ</a>
            </div>
        </div>
    </footer>
    </div>
    </div>

    @include('partials.newsletter-popup')

    @include('partials.recent-products')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.Dressingue?.initCatalog?.();
        });
    </script>
</body>
</html>

