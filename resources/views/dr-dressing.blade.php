<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    @include('partials.fixed-viewport')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solutions King | Dressings et armoires</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="brand-body storefront-fixed-body min-h-screen antialiased">
    <header class="brand-header sticky top-0 z-50" data-mobile-header>
        <div class="mx-auto flex max-w-7xl flex-nowrap items-center justify-between gap-2 px-4 py-2 sm:px-6 lg:gap-2.5 lg:px-8">
            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    class="brand-mobile-menu-toggle inline-flex h-9 w-9 items-center justify-center rounded-md border border-[rgba(239,224,204,0.25)] text-[0.92rem] text-[var(--brand-sand-soft)] lg:hidden"
                    aria-controls="dr-mobile-menu"
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

            <nav class="hidden items-center gap-4 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-[var(--brand-sand-soft)] lg:flex">
                <a href="{{ route('catalog.index') }}" class="brand-nav-link">Accueil</a>
                @foreach ($miniCategories as $section)
                    <a href="#{{ $section['slug'] }}" class="brand-nav-link">{{ $section['label'] }}</a>
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('catalog.index') }}" class="brand-header-button px-2.5 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em]">
                    Retour accueil
                </a>
            </div>
        </div>

        <div id="dr-mobile-menu" class="brand-mobile-menu lg:hidden" data-mobile-menu hidden>
            <div class="mx-auto grid max-w-7xl gap-2 px-4 pb-4 pt-3 sm:px-6">
                <a href="{{ route('catalog.index') }}" class="brand-mobile-menu-link">Accueil</a>
                @foreach ($miniCategories as $section)
                    <a href="#{{ $section['slug'] }}" class="brand-mobile-menu-link">{{ $section['label'] }}</a>
                @endforeach
                <a href="{{ route('catalog.index') }}" class="brand-mobile-menu-link">Retour accueil</a>
            </div>
        </div>
    </header>

    <div class="storefront-fixed-frame" data-storefront-fixed-frame>
    <div class="storefront-fixed-stage" data-storefront-fixed-stage data-storefront-stage-width="1280">
    <main class="brand-page-enter">
        @include('partials.storefront-notices')

        @include('partials.flash-campaign-banner', ['campaign' => $activeFlashCampaign ?? null])

        @include('partials.loyalty-banner', ['summary' => $loyaltySummary ?? null])

        @if ($catalogFilters['q'] !== '' && count($searchResults) > 0)
            <section class="brand-section-light py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="brand-search-spotlight p-6 sm:p-8" data-reveal>
                        <div class="storefront-fixed-row storefront-fixed-row-between storefront-fixed-align-end flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                            <div class="max-w-3xl">
                                <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F50E;</span> Recherche active</p>
                                <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">
                                    Résultats pour "{{ $catalogFilters['q'] }}"
                                </h2>
                                <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">
                                    Les resultats trouves dans les solutions King apparaissent ici avant les categories pour etre vus tout de suite.
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

        @foreach ($miniCategories as $section)
            <section id="{{ $section['slug'] }}" class="{{ $loop->odd ? 'brand-section-light' : 'brand-section-muted' }} py-12">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-10 max-w-3xl">
                        <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4C1;</span> Collection</p>
                        <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $section['label'] }}</h2>
                        <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">
                            {{ $section['description'] }}
                        </p>
                    </div>

                    <div class="storefront-fixed-grid-3 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @forelse ($section['products'] as $product)
                            @include('partials.storefront-product-card', ['product' => $product])
                        @empty
                            <div class="brand-empty-state p-6 text-sm leading-7 text-[var(--brand-copy)]">
                                Aucun produit disponible dans cette catégorie avec les filtres actuels.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        @endforeach

        <section id="conseils" class="brand-section-light py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 max-w-3xl">
                    <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x2139;&#xFE0F;</span> Guide d'achat & conseils</p>
                    <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">Des repères pour choisir comme sur une vraie fiche marchande</h2>
                    <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">
                        Les titres produits servent ici de liens directs vers les fiches, pour supprimer le vieux réflexe « voir plus » et rendre la navigation plus naturelle.
                    </p>
                </div>

                <div class="storefront-fixed-grid-2 grid gap-6 lg:grid-cols-2">
                    @foreach ($buyingGuides as $guide)
                        <article class="brand-guide-card p-6" data-reveal>
                            <p class="brand-kicker brand-kicker-light">{{ $guide['eyebrow'] }}</p>
                            <h3 class="mt-4 text-2xl font-semibold text-[var(--brand-ink)]">{{ $guide['title'] }}</h3>
                            <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                                {{ $guide['description'] }}
                            </p>
                            <div class="mt-5 flex flex-wrap gap-3">
                                @foreach ($guide['links'] as $link)
                                    <a href="{{ $link['url'] }}" class="brand-guide-link text-sm font-semibold">
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer class="brand-footer py-8">
        @include('partials.social-links')
        
        <div class="storefront-fixed-footer mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm text-[var(--brand-sand-soft)] sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="flex items-center gap-4">
                <x-brand-logo class="brand-logo-header" compact />
                <p>Solutions King regroupe les categories les plus utiles pour cette page : dressings et armoires.</p>
            </div>
            <div class="flex flex-wrap gap-5">
                <a href="{{ route('catalog.index') }}" class="brand-nav-link">Accueil</a>
            </div>
        </div>
    </footer>
    </div>
    </div>

    @include('partials.newsletter-popup')

</body>
</html>

