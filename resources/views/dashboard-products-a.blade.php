<x-app-layout>
    <x-slot:header>
        <div class="admin-page-header">
            <div>
                <p class="admin-kicker">Administration</p>
                <h1 class="admin-title mt-3">Produit A</h1>
                <p class="admin-copy mt-3 max-w-3xl">
                    Page dédiée à l'édition du produit A, avec le même langage visuel que le storefront et le dashboard principal.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard.products') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Tous les produits
                </a>
                <a href="{{ route('dashboard') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Dashboard
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
                Vérifiez les champs du formulaire puis réessayez.
            </div>
        @endif

        <section class="admin-surface admin-surface-muted px-6 py-6 sm:px-8">
            <div class="grid gap-4 md:grid-cols-3">
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Produit</p>
                    <p class="mt-4 text-xl font-semibold text-[var(--brand-ink)]">{{ $product->name }}</p>
                    <p class="admin-stat-copy mt-3">Code {{ $product->code }} sur le catalogue public.</p>
                </article>

                <article class="admin-stat-card">
                    <p class="admin-stat-label">Précommandes</p>
                    <p class="admin-stat-value mt-4">{{ (int) $product->preorder_count }}</p>
                    <p class="admin-stat-copy mt-3">État actuel du produit A côté administration.</p>
                </article>

                <article class="admin-stat-card">
                    <p class="admin-stat-label">Avis</p>
                    <p class="admin-stat-value mt-4">{{ (int) ($product->reviews_count ?? 0) }}</p>
                    <p class="admin-stat-copy mt-3">Commentaires visibles liés au produit A.</p>
                </article>
            </div>
        </section>

        <section class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Édition</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Contenu public du produit A</h2>
                </div>

                <p class="admin-copy max-w-2xl">
                    Toutes les zones éditées ici sont pensées pour rester parfaitement raccord avec l'accueil, la fiche produit et le checkout.
                </p>
            </div>

            <div class="mt-8">
                <x-admin-product-editor
                    :product="$product"
                    :images="$productMedia['images']"
                    :open="true"
                    :public-url="route('products.show', $product)"
                />
            </div>
        </section>
    </div>
</x-app-layout>

