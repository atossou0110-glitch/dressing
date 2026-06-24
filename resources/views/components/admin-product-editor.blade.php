@props([
    'product',
    'images' => [],
    'open' => false,
    'publicUrl' => null,
])

@php
    $content = $product->resolvedContent();
    $isOldInputTarget = (string) old('form_product_id') === (string) $product->id;
    $isExpanded = $isOldInputTarget || $open;
    $publicProductUrl = $publicUrl ?: route('products.show', $product);
    $currentCategory = $isOldInputTarget ? old('category', $product->category) : $product->category;
    $isVisibleOnStorefront = ! in_array($currentCategory, \App\Models\Product::hiddenStorefrontCategories(), true);
@endphp

<details id="product-editor-{{ $product->id }}" data-dashboard-product class="admin-details" @if ($isExpanded) open @endif>
    <summary class="admin-summary">
        <div>
            <p class="admin-kicker">Produit {{ $product->code }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-[var(--brand-ink)]">{{ $product->name }}</h3>
            <p class="admin-summary-meta mt-2">
                {{ $product->reviews_count ?? 0 }} avis - Slug {{ $product->slug }} - Categorie {{ $currentCategory }}
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <span class="admin-pill">{{ $isVisibleOnStorefront ? 'Visible sur le site' : 'Masque du site' }}</span>
            <span class="admin-summary-toggle">Afficher / masquer</span>
            <span class="admin-pill">{{ $product->resolvedContent()['home_price'] }}</span>
        </div>
    </summary>

    <div class="mt-6 space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="admin-copy max-w-2xl">
                Modifiez le contenu public encore visible, les interactions suivies et les images de ce produit dans une interface alignee sur le storefront actuel.
            </p>

            <a href="{{ $publicProductUrl }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                Ouvrir la fiche publique
            </a>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_product_id" value="{{ $product->id }}">

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name-{{ $product->id }}" class="admin-field-label">Nom du produit</label>
                    <input
                        id="name-{{ $product->id }}"
                        name="name"
                        type="text"
                        value="{{ $isOldInputTarget ? old('name', $product->name) : $product->name }}"
                        class="admin-input"
                    >
                </div>

                <div>
                    <label for="category-{{ $product->id }}" class="admin-field-label">Categorie storefront</label>
                    <select
                        id="category-{{ $product->id }}"
                        name="category"
                        class="admin-input"
                    >
                        @foreach (['commode' => 'Commode', 'etagere' => 'Etagere', 'dressing' => 'Dressing', 'armoire' => 'Armoire'] as $value => $label)
                            <option value="{{ $value }}" @selected($currentCategory === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="admin-helper mt-2">
                        Cette categorie apparait sur le site public.
                    </p>
                </div>

                <div>
                    <label for="preorder-count-{{ $product->id }}" class="admin-field-label">Précommandes</label>
                    <input
                        id="preorder-count-{{ $product->id }}"
                        name="preorder_count"
                        type="number"
                        min="0"
                        value="{{ $isOldInputTarget ? old('preorder_count', (int) $product->preorder_count) : (int) $product->preorder_count }}"
                        class="admin-input"
                    >
                </div>
            </div>

            <div class="admin-section-divider space-y-5">
                <div>
                    <p class="admin-kicker">Accueil</p>
                    <h4 class="mt-2 text-xl font-semibold text-[var(--brand-ink)]">Contenu des cartes storefront</h4>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="home-badge-{{ $product->id }}" class="admin-field-label">Badge accueil</label>
                        <input
                            id="home-badge-{{ $product->id }}"
                            name="home_badge"
                            type="text"
                            value="{{ $isOldInputTarget ? old('home_badge', $content['home_badge']) : $content['home_badge'] }}"
                            class="admin-input"
                        >
                    </div>

                    <div>
                        <label for="home-price-{{ $product->id }}" class="admin-field-label">Prix affiché</label>
                        <input
                            id="home-price-{{ $product->id }}"
                            name="home_price"
                            type="text"
                            value="{{ $isOldInputTarget ? old('home_price', $content['home_price']) : $content['home_price'] }}"
                            class="admin-input"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="home-highlight-{{ $product->id }}" class="admin-field-label">Benefice court</label>
                        <input
                            id="home-highlight-{{ $product->id }}"
                            name="home_highlight"
                            type="text"
                            value="{{ $isOldInputTarget ? old('home_highlight', $content['home_highlight']) : $content['home_highlight'] }}"
                            class="admin-input"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="home-description-{{ $product->id }}" class="admin-field-label">Description accueil</label>
                        <textarea
                            id="home-description-{{ $product->id }}"
                            name="home_description"
                            rows="3"
                            class="admin-textarea"
                        >{{ $isOldInputTarget ? old('home_description', $content['home_description']) : $content['home_description'] }}</textarea>
                    </div>

                </div>
            </div>

            <div class="admin-section-divider space-y-5">
                <div>
                    <p class="admin-kicker">Fiche produit</p>
                    <h4 class="mt-2 text-xl font-semibold text-[var(--brand-ink)]">Contenu detaille de la vue publique</h4>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="detail-badge-{{ $product->id }}" class="admin-field-label">Badge fiche produit</label>
                        <input
                            id="detail-badge-{{ $product->id }}"
                            name="detail_badge"
                            type="text"
                            value="{{ $isOldInputTarget ? old('detail_badge', $content['detail_badge']) : $content['detail_badge'] }}"
                            class="admin-input"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="detail-subtitle-{{ $product->id }}" class="admin-field-label">Sous-titre produit</label>
                        <textarea
                            id="detail-subtitle-{{ $product->id }}"
                            name="detail_subtitle"
                            rows="2"
                            class="admin-textarea"
                        >{{ $isOldInputTarget ? old('detail_subtitle', $content['detail_subtitle']) : $content['detail_subtitle'] }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="detail-description-{{ $product->id }}" class="admin-field-label">Description principale</label>
                        <textarea
                            id="detail-description-{{ $product->id }}"
                            name="detail_description"
                            rows="4"
                            class="admin-textarea"
                        >{{ $isOldInputTarget ? old('detail_description', $content['detail_description']) : $content['detail_description'] }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="features-text-{{ $product->id }}" class="admin-field-label">Points forts commerciaux</label>
                        <textarea
                            id="features-text-{{ $product->id }}"
                            name="features_text"
                            rows="5"
                            class="admin-textarea"
                        >{{ $isOldInputTarget ? old('features_text', implode("\n", $content['features'])) : implode("\n", $content['features']) }}</textarea>
                        <p class="admin-helper mt-2">Une ligne par benefice client.</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="specifications-text-{{ $product->id }}" class="admin-field-label">Caractéristiques techniques</label>
                        <textarea
                            id="specifications-text-{{ $product->id }}"
                            name="specifications_text"
                            rows="6"
                            class="admin-textarea"
                        >{{ $isOldInputTarget ? old('specifications_text', implode("\n", $content['specifications'])) : implode("\n", $content['specifications']) }}</textarea>
                        <p class="admin-helper mt-2">Une ligne par caractéristique.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Enregistrer
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.products.reset', $product) }}" onsubmit="return confirm('Remettre à zéro les interactions suivies pour ce produit ?');">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-[999px] border border-[rgba(176,73,68,0.22)] bg-[rgba(255,241,239,0.8)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-[#8b3733] transition hover:bg-[rgba(255,232,230,0.95)]">
                Remettre à zéro
            </button>
        </form>

        <div class="admin-section-divider space-y-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="admin-kicker">Galerie</p>
                    <h4 class="mt-2 text-xl font-semibold text-[var(--brand-ink)]">Images du produit</h4>
                </div>
                <span class="admin-pill">{{ count($images) }} image(s)</span>
            </div>

            <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="images-{{ $product->id }}" class="admin-field-label">Ajouter de nouvelles images</label>
                    <input
                        id="images-{{ $product->id }}"
                        name="images[]"
                        type="file"
                        accept=".png,.jpg,.jpeg,.webp"
                        multiple
                        class="admin-file-input"
                    >
                    <p class="admin-helper mt-2">
                        Les images envoyees ici seront utilisees directement sur les vues publiques de ce produit.
                    </p>
                </div>

                <button type="submit" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Envoyer les images
                </button>
            </form>

            @if (count($images) > 0)
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($images as $image)
                        <article class="admin-media-card">
                            <div class="admin-media-frame">
                                <img src="{{ $image['url'] }}" alt="{{ $image['filename'] }}">
                            </div>

                            <div class="space-y-3 p-4">
                                <p class="truncate text-sm font-semibold text-[var(--brand-ink)]">{{ $image['filename'] }}</p>

                                @if ($image['managed'])
                                    <p class="admin-helper">Ajoutee le {{ $image['updated_at'] }}</p>

                                    <form method="POST" action="{{ route('admin.products.images.destroy', $product) }}" onsubmit="return confirm('Supprimer cette image ?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="filename" value="{{ $image['filename'] }}">
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-[999px] border border-[rgba(176,73,68,0.22)] bg-[rgba(255,241,239,0.8)] px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.12em] text-[#8b3733] transition hover:bg-[rgba(255,232,230,0.95)]">
                                            Supprimer cette image
                                        </button>
                                    </form>
                                @else
                                    <p class="admin-helper">Image de secours actuellement utilisee.</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="admin-empty-state">
                    Aucune image geree depuis le dashboard pour ce produit pour le moment.
                </div>
            @endif
        </div>
    </div>
</details>

