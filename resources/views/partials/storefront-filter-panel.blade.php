<section class="brand-section-light py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <form action="{{ $actionUrl ?? url()->current() }}" method="GET" class="brand-filter-panel p-5 sm:p-6" data-reveal>
            <div class="flex flex-col gap-6 xl:flex-row xl:items-end">
                <div class="max-w-xl">
                    <p class="brand-kicker brand-kicker-light">{{ $kicker ?? 'Recherche' }}</p>
                    <h2 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">{{ $title }}</h2>
                    <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
                        {{ $description }}
                    </p>
                </div>

                <div class="grid flex-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <label class="brand-filter-label">
                        <span>Recherche</span>
                        <input
                            type="text"
                            name="q"
                            value="{{ $catalogFilters['q'] }}"
                            class="brand-input brand-filter-control w-full px-4 py-3 text-sm"
                            placeholder="Nom, categorie ou ambiance"
                        >
                    </label>

                    @if (! empty($catalogFilters['collectionOptions']))
                        <label class="brand-filter-label">
                            <span>Collection</span>
                            <select name="collection" class="brand-input brand-filter-control w-full px-4 py-3 text-sm">
                                <option value="">Toutes les collections</option>
                                @foreach ($catalogFilters['collectionOptions'] as $option)
                                    <option value="{{ $option['value'] }}" @selected($catalogFilters['collection'] === $option['value'])>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    @endif

                    <label class="brand-filter-label">
                        <span>Categorie</span>
                        <select name="category" class="brand-input brand-filter-control w-full px-4 py-3 text-sm">
                            <option value="">Toutes les categories</option>
                            @foreach ($catalogFilters['categoryOptions'] as $option)
                                <option value="{{ $option['value'] }}" @selected($catalogFilters['category'] === $option['value'])>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="brand-filter-label">
                        <span>Budget</span>
                        <select name="budget" class="brand-input brand-filter-control w-full px-4 py-3 text-sm">
                            <option value="">Tous les budgets</option>
                            @foreach ($catalogFilters['budgetOptions'] as $option)
                                <option value="{{ $option['value'] }}" @selected($catalogFilters['budget'] === $option['value'])>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-4 border-t border-[var(--brand-line)] pt-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-3 text-sm text-[var(--brand-copy)]">
                    <span class="brand-filter-result">{{ $catalogFilters['resultsCount'] }} produit(s) visible(s)</span>
                    @if ($catalogFilters['hasActiveFilters'])
                        <span class="text-[var(--brand-teal-soft)]">Filtres actifs</span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                        Filtrer
                    </button>
                    @if ($catalogFilters['hasActiveFilters'])
                        <a href="{{ $resetUrl ?? url()->current() }}" class="brand-header-button inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>
