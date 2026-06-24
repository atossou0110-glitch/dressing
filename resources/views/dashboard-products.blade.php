<x-app-layout>
    @php
        $restoreCreateInput = old('form_product_id') === null;
        $createValue = fn (string $key, string $default = '') => $restoreCreateInput ? old($key, $default) : $default;
    @endphp

    <x-slot:header>
        <div class="admin-page-header">
            <div>
                <p class="admin-kicker">Administration produits</p>
                <h1 class="admin-title mt-3">Gestion complete du catalogue</h1>
                <p class="admin-copy mt-3 max-w-4xl">
                    Cette page vous permet de creer un produit, mettre a jour les contenus encore visibles sur le site, gerer les compteurs et piloter les images sans conserver les anciens champs retires du storefront.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Retour dashboard
                </a>
                <a href="{{ route('catalog.index') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Voir le site
                </a>
            </div>
        </div>
    </x-slot:header>

    <div class="space-y-8">
        @if (session('status'))
            <div class="admin-status admin-status-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-status admin-status-error">
                Le formulaire n'a pas pu etre enregistre. Corrigez les champs puis relancez l'action.
            </div>
        @endif

        <section class="admin-surface admin-surface-muted px-6 py-6 sm:px-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Produits</p>
                    <p class="admin-stat-value mt-4">{{ $productStats['total'] }}</p>
                    <p class="admin-stat-copy mt-3">{{ $productStats['visible'] }} visibles.</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Masques</p>
                    <p class="admin-stat-value mt-4">{{ $productStats['hidden'] }}</p>
                    <p class="admin-stat-copy mt-3">Produits masques du storefront.</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Precommandes</p>
                    <p class="admin-stat-value mt-4">{{ $productStats['preorders'] }}</p>
                    <p class="admin-stat-copy mt-3">Demandes WhatsApp ou trackees.</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Avis</p>
                    <p class="admin-stat-value mt-4">{{ $productStats['reviews'] }}</p>
                    <p class="admin-stat-copy mt-3">Commentaires rattaches aux produits.</p>
                </article>
            </div>
        </section>

        <section class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Creation</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Ajouter un nouveau produit</h2>
                </div>

                <p class="admin-copy max-w-3xl">
                    Le produit cree ici rejoint automatiquement le catalogue admin avec uniquement les informations encore utilisees par les vues publiques.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" class="mt-8 space-y-6">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="new-product-slug" class="admin-field-label">Slug</label>
                        <input id="new-product-slug" name="slug" type="text" value="{{ $createValue('slug') }}" class="admin-input" placeholder="produit-i">
                    </div>

                    <div>
                        <label for="new-product-code" class="admin-field-label">Code</label>
                        <input id="new-product-code" name="code" type="text" value="{{ $createValue('code') }}" class="admin-input" maxlength="1" placeholder="I">
                    </div>

                    <div>
                        <label for="new-product-category" class="admin-field-label">Categorie</label>
                        <select id="new-product-category" name="category" class="admin-select">
                            @foreach ($categoryOptions as $value => $label)
                                <option value="{{ $value }}" @selected($createValue('category', 'commode') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 xl:col-span-1">
                        <label for="new-product-name" class="admin-field-label">Nom</label>
                        <input id="new-product-name" name="name" type="text" value="{{ $createValue('name') }}" class="admin-input" placeholder="Bibliotheque Capsule">
                    </div>

                    <div>
                        <label for="new-home-badge" class="admin-field-label">Badge accueil</label>
                        <input id="new-home-badge" name="home_badge" type="text" value="{{ $createValue('home_badge') }}" class="admin-input">
                    </div>

                    <div>
                        <label for="new-home-price" class="admin-field-label">Prix</label>
                        <input id="new-home-price" name="home_price" type="text" value="{{ $createValue('home_price') }}" class="admin-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="new-home-highlight" class="admin-field-label">Benefice court</label>
                        <input id="new-home-highlight" name="home_highlight" type="text" value="{{ $createValue('home_highlight') }}" class="admin-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="new-home-description" class="admin-field-label">Description accueil</label>
                        <textarea id="new-home-description" name="home_description" rows="3" class="admin-textarea">{{ $createValue('home_description') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="new-detail-badge" class="admin-field-label">Badge fiche produit</label>
                        <input id="new-detail-badge" name="detail_badge" type="text" value="{{ $createValue('detail_badge') }}" class="admin-input">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="new-detail-subtitle" class="admin-field-label">Sous-titre fiche</label>
                        <textarea id="new-detail-subtitle" name="detail_subtitle" rows="2" class="admin-textarea">{{ $createValue('detail_subtitle') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="new-detail-description" class="admin-field-label">Description detail</label>
                        <textarea id="new-detail-description" name="detail_description" rows="4" class="admin-textarea">{{ $createValue('detail_description') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="new-features-text" class="admin-field-label">Points forts commerciaux</label>
                        <textarea id="new-features-text" name="features_text" rows="5" class="admin-textarea">{{ $createValue('features_text') }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="new-specifications-text" class="admin-field-label">Caracteristiques</label>
                        <textarea id="new-specifications-text" name="specifications_text" rows="5" class="admin-textarea">{{ $createValue('specifications_text') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Creer le produit
                </button>
            </form>
        </section>

        <section class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Edition globale</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Tous les produits du catalogue</h2>
                </div>

                <p class="admin-copy max-w-3xl">
                    Chaque fiche ci-dessous controle le contenu public encore actif sur l'accueil, les fiches produit et le checkout.
                </p>
            </div>

            <div class="mt-8 space-y-6">
                @foreach ($products as $product)
                    <x-admin-product-editor
                        :product="$product"
                        :images="$productMedia[$product->id]['images'] ?? []"
                        :open="$loop->first"
                        :public-url="route('products.show', $product)"
                    />
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
