@props([
    'products' => [],
])

@php
    $toolProducts = collect($products)
        ->filter(fn ($product) => filled($product['slug'] ?? null))
        ->map(function ($product) {
            $dimensions = is_array($product['dimensions'] ?? null) ? $product['dimensions'] : null;

            return [
                'slug' => (string) ($product['slug'] ?? ''),
                'code' => (string) ($product['code'] ?? ''),
                'name' => (string) ($product['name'] ?? 'Produit'),
                'collection' => (string) ($product['collection'] ?? ''),
                'collectionLabel' => (string) ($product['collectionLabel'] ?? 'Collection'),
                'categoryLabel' => (string) ($product['categoryLabel'] ?? 'Categorie'),
                'homePrice' => (string) ($product['homePrice'] ?? 'Prix sur demande'),
                'priceValue' => isset($product['priceValue']) ? (int) $product['priceValue'] : null,
                'detailsUrl' => (string) ($product['detailsUrl'] ?? '#'),
                'imageUrl' => (string) ($product['imageUrl'] ?? ''),
                'dimensions' => $dimensions ? [
                    'width' => (float) ($dimensions['width'] ?? 0),
                    'height' => (float) ($dimensions['height'] ?? 0),
                    'depth' => (float) ($dimensions['depth'] ?? 0),
                    'label' => (string) ($dimensions['label'] ?? ''),
                ] : null,
                'dimensionsLabel' => $dimensions['label'] ?? 'Dimensions non communiquees',
            ];
        })
        ->values();

    $toolProductsList = $toolProducts->all();
    $defaultComparatorSlugs = $toolProducts->pluck('slug')->take(2)->values()->all();
@endphp

<section class="brand-section-light py-16" data-decision-tools>
    <script type="application/json" data-decision-products>@json($toolProductsList)</script>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 max-w-3xl" data-reveal>
            <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F9ED;</span> Aide a la decision</p>
            <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">Des outils utiles pour se projeter avant l achat</h2>
            <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">
                Les visiteurs hesitent moins quand ils peuvent verifier les dimensions, comparer les options et visualiser la place du meuble chez eux.
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <button type="button" class="brand-tool-card w-full text-left" data-decision-tool-open="dimension-calculator-modal" data-reveal>
                <span class="brand-tool-icon text-base font-semibold uppercase tracking-[0.18em]">&#x1F4CF;</span>
                <p class="brand-kicker brand-kicker-light mt-6">Mesure</p>
                <h3 class="mt-3 text-xl font-semibold text-[var(--brand-ink)]">Calculateur de dimensions</h3>
                <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                    Verifiez quels meubles passent reellement dans votre espace en fonction de la largeur et de la hauteur disponibles.
                </p>
                <span class="brand-button mt-6 px-5 py-3 text-xs font-semibold uppercase tracking-[0.14em]">Calculer</span>
            </button>

            <button type="button" class="brand-tool-card w-full text-left" data-decision-tool-open="size-guide-modal" data-reveal>
                <span class="brand-tool-icon text-base font-semibold uppercase tracking-[0.18em]">&#x1F4D0;</span>
                <p class="brand-kicker brand-kicker-light mt-6">Guide</p>
                <h3 class="mt-3 text-xl font-semibold text-[var(--brand-ink)]">Guide des dimensions</h3>
                <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                    Apprenez a prendre les bonnes mesures et evitez les erreurs avant la validation de commande.
                </p>
                <span class="brand-guide-link mt-6 text-xs font-semibold uppercase tracking-[0.14em]">Voir le guide</span>
            </button>

            <button type="button" class="brand-tool-card w-full text-left" data-decision-tool-open="comparator-modal" data-reveal>
                <span class="brand-tool-icon text-base font-semibold uppercase tracking-[0.18em]">&#x1F4CA;</span>
                <p class="brand-kicker brand-kicker-light mt-6">Choix</p>
                <h3 class="mt-3 text-xl font-semibold text-[var(--brand-ink)]">Comparateur</h3>
                <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                    Comparez maintenant plusieurs produits de la page sur leur format, leur prix et leur usage.
                </p>
                <span class="brand-guide-link mt-6 text-xs font-semibold uppercase tracking-[0.14em]">Comparer</span>
            </button>

        </div>
    </div>
</section>

<div id="dimension-calculator-modal" class="brand-modal-shell fixed inset-0 z-50 hidden items-center justify-center bg-[rgba(2,25,31,0.58)] px-4 py-6" aria-hidden="true">
    <div class="brand-modal-card relative w-full max-w-4xl p-8">
        <button type="button" data-decision-tool-close="dimension-calculator-modal" class="brand-modal-close absolute right-5 top-5 inline-flex h-10 w-10 items-center justify-center">
            x
        </button>

        <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4D0;</span> Outil 01</p>
        <h2 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Calculateur de dimensions</h2>
        <p class="mt-4 max-w-2xl text-sm leading-7 text-[var(--brand-copy)]">
            Indiquez votre largeur et votre hauteur disponibles. Nous vous montrons les meubles de cette page qui peuvent entrer dans votre espace.
        </p>

        <div class="mt-8 grid gap-5 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]" for="space-width">
                    Largeur disponible (cm)
                </label>
                <input type="number" id="space-width" min="1" placeholder="Ex: 280" class="brand-input w-full px-4 py-3 text-sm">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]" for="space-height">
                    Hauteur disponible (cm)
                </label>
                <input type="number" id="space-height" min="1" placeholder="Ex: 240" class="brand-input w-full px-4 py-3 text-sm">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]" for="space-collection">
                    Collection
                </label>
                <select id="space-collection" class="brand-input w-full px-4 py-3 text-sm">
                    <option value="">Toutes les collections</option>
                                <option value="rangement">Rangements</option>
                    <option value="dressing">Dressing</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <button type="button" data-dimension-calculate class="brand-button w-full py-3 text-sm font-semibold uppercase tracking-[0.14em] sm:w-auto sm:min-w-[16rem]">
                Verifier les options
            </button>
            <p class="text-sm leading-7 text-[var(--brand-copy)]">
                Astuce: gardez idealement 5 cm de marge supplementaire pour la pose et l ouverture.
            </p>
        </div>

        <div id="compatibility-result" class="brand-modal-tip mt-6 hidden p-5">
            <div data-dimension-summary class="text-sm leading-7 text-[var(--brand-copy)]"></div>
            <div data-dimension-matches class="mt-5 grid gap-4 md:grid-cols-2"></div>
        </div>
    </div>
</div>

<div id="size-guide-modal" class="brand-modal-shell fixed inset-0 z-50 hidden items-center justify-center bg-[rgba(2,25,31,0.58)] px-4 py-6" aria-hidden="true">
    <div class="brand-modal-card relative w-full max-w-2xl p-8">
        <button type="button" data-decision-tool-close="size-guide-modal" class="brand-modal-close absolute right-5 top-5 inline-flex h-10 w-10 items-center justify-center">
            x
        </button>

        <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4D8;</span> Outil 02</p>
        <h2 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Guide des dimensions</h2>

        <div class="mt-8 space-y-6">
            <div class="brand-modal-tip p-5">
                <h3 class="text-lg font-semibold text-[var(--brand-ink)]">Comment mesurer la largeur</h3>
                <p class="mt-3 text-sm leading-7 text-[var(--brand-copy)]">
                    Mesurez d un mur a l autre sur la zone utile, puis gardez un peu d aisance pour les portes et les mouvements autour du meuble.
                </p>
            </div>

            <div class="brand-modal-tip p-5">
                <h3 class="text-lg font-semibold text-[var(--brand-ink)]">Comment mesurer la hauteur</h3>
                <p class="mt-3 text-sm leading-7 text-[var(--brand-copy)]">
                    Mesurez du sol au plafond et notez les éléments qui peuvent limiter l'installation comme une fenêtre, une retombée ou une prise.
                </p>
            </div>

            <div class="brand-modal-tip p-5">
                <h3 class="text-lg font-semibold text-[var(--brand-ink)]">Vérifier la profondeur</h3>
                <p class="mt-3 text-sm leading-7 text-[var(--brand-copy)]">
                    Même si la largeur et la hauteur conviennent, gardez aussi un œil sur la profondeur pour assurer une circulation confortable.
                </p>
            </div>

            <div class="brand-modal-tip p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--brand-sand-dark)]"><span aria-hidden="true">&#x1F4A1;</span> Conseil pratique</p>
                <p class="mt-3 text-sm leading-7 text-[var(--brand-copy)]">
                    Ajoutez environ 5 cm de marge de sécurité pour l'installation, la ventilation et une ouverture confortable.
                </p>
            </div>
        </div>
    </div>
</div>

<div id="comparator-modal" class="brand-modal-shell fixed inset-0 z-50 hidden items-center justify-center bg-[rgba(2,25,31,0.58)] px-4 py-6" aria-hidden="true">
    <div class="brand-modal-card relative w-full max-w-6xl p-8">
        <button type="button" data-decision-tool-close="comparator-modal" class="brand-modal-close absolute right-5 top-5 inline-flex h-10 w-10 items-center justify-center">
            x
        </button>

        <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x1F4CA;</span> Outil 03</p>
        <h2 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Comparateur de produits</h2>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-[var(--brand-copy)]">
            Sélectionnez jusqu'a trois produits de cette page pour voir en un coup d'oeil leur collection, leurs dimensions et leur prix.
        </p>

        <div class="mt-8 grid gap-5 lg:grid-cols-3">
            @for ($index = 0; $index < 3; $index++)
                <div>
                    <label class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]" for="compare-product-{{ $index + 1 }}">
                        Produit {{ $index + 1 }}
                    </label>
                    <select
                        id="compare-product-{{ $index + 1 }}"
                        class="brand-input w-full px-4 py-3 text-sm"
                        data-compare-select
                    >
                        <option value="">Aucun produit</option>
                        @foreach ($toolProductsList as $product)
                            <option
                                value="{{ $product['slug'] }}"
                                @selected(($defaultComparatorSlugs[$index] ?? '') === $product['slug'])
                            >
                                {{ $product['code'] }} - {{ $product['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endfor
        </div>

        <div class="brand-modal-tip mt-6 p-5">
            <p class="text-sm leading-7 text-[var(--brand-copy)]">
                Le tableau ci-dessous se met a jour automatiquement quand vous changez la selection.
            </p>
        </div>

        <div id="comparison-table" class="mt-6 overflow-x-auto">
            <p class="text-sm text-[var(--brand-copy)]">Aucun produit selectionne pour la comparaison.</p>
        </div>
    </div>
</div>

