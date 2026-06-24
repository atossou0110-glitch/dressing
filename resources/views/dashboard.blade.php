<x-app-layout>
    <x-slot:header>
        <div class="admin-page-header">
            <div>
                <p class="admin-kicker">Administration centrale</p>
                <h1 class="admin-title mt-3">Centre de pilotage du site</h1>
                <p class="admin-copy mt-3 max-w-4xl">
                    Cette vue centralise l'analyse des pages publiques, les modules actifs et les commandes qui vous permettent de piloter le catalogue, le paiement, l'engagement et le support depuis un seul endroit.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard.products') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Gerer les produits
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
                Une action n'a pas pu etre terminee. Verifiez le formulaire concerne puis recommencez.
            </div>
        @endif

        <section class="admin-surface admin-surface-muted px-6 py-6 sm:px-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($overview as $card)
                    <article class="admin-stat-card">
                        <p class="admin-stat-label">{{ $card['label'] }}</p>
                        <p class="admin-stat-value mt-4">{{ $card['value'] }}</p>
                        <p class="admin-stat-copy mt-3">{{ $card['copy'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Section Exports Rapides -->
        <section class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Exports</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Télécharger les données</h2>
                </div>
            </div>

            <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <a href="{{ route('admin.export.products') }}" class="admin-link-card rounded-[1.25rem] border border-[var(--brand-line)] bg-white/80 p-4 text-center hover:bg-[var(--brand-panel)] transition">
                    <p class="text-2xl mb-2">📦</p>
                    <h3 class="font-semibold text-[var(--brand-ink)]">Produits</h3>
                    <p class="admin-copy text-xs mt-2">Tous les produits en CSV</p>
                </a>

                <a href="{{ route('admin.export.orders') }}" class="admin-link-card rounded-[1.25rem] border border-[var(--brand-line)] bg-white/80 p-4 text-center hover:bg-[var(--brand-panel)] transition">
                    <p class="text-2xl mb-2">🛒</p>
                    <h3 class="font-semibold text-[var(--brand-ink)]">Commandes</h3>
                    <p class="admin-copy text-xs mt-2">Détail de toutes les ventes</p>
                </a>

                <a href="{{ route('admin.export.clients') }}" class="admin-link-card rounded-[1.25rem] border border-[var(--brand-line)] bg-white/80 p-4 text-center hover:bg-[var(--brand-panel)] transition">
                    <p class="text-2xl mb-2">👥</p>
                    <h3 class="font-semibold text-[var(--brand-ink)]">Clients</h3>
                    <p class="admin-copy text-xs mt-2">Subscribers et emails</p>
                </a>

                <a href="{{ route('admin.export.audit-logs') }}" class="admin-link-card rounded-[1.25rem] border border-[var(--brand-line)] bg-white/80 p-4 text-center hover:bg-[var(--brand-panel)] transition">
                    <p class="text-2xl mb-2">📋</p>
                    <h3 class="font-semibold text-[var(--brand-ink)]">Audit Logs</h3>
                    <p class="admin-copy text-xs mt-2">Historique des mods</p>
                </a>
            </div>
        </section>

        <section class="admin-surface px-6 py-6 sm:px-8">
            <div class="flex flex-wrap gap-3">
                <a href="#site-analysis" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                    Analyse du site
                </a>
                <a href="#study-report" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                    Rapport d'etude
                </a>
                <a href="#orders" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                    Commandes
                </a>
                <a href="#engagement" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                    Engagement
                </a>
                <a href="#settings" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                    WhatsApp
                </a>
                <a href="#reviews" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                    Avis recents
                </a>
                <a href="#support" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                    Support
                </a>
            </div>
        </section>

        <section id="site-analysis" class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Analyse totale</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Vues du projet et fonctionnalites actives</h2>
                </div>

                <p class="admin-copy max-w-3xl">
                    Voici la cartographie des vues qui composent le site et les briques fonctionnelles deja en production. Cela vous donne une lecture rapide de tout ce que le dashboard doit controler.
                </p>
            </div>

            <div class="mt-8 grid gap-4 xl:grid-cols-2">
                <div class="rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                    <p class="admin-kicker">Vues du site</p>
                    <div class="mt-4 space-y-4">
                        @foreach ($viewMap as $view)
                            <article class="rounded-[1.25rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="text-lg font-semibold text-[var(--brand-ink)]">{{ $view['name'] }}</h3>
                                    <span class="admin-pill">{{ $view['path'] }}</span>
                                </div>
                                <p class="admin-copy mt-3">{{ $view['purpose'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                    <p class="admin-kicker">Fonctionnalites</p>
                    <div class="mt-4 space-y-4">
                        @foreach ($featureMap as $feature)
                            <article class="rounded-[1.25rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-4">
                                <h3 class="text-lg font-semibold text-[var(--brand-ink)]">{{ $feature['name'] }}</h3>
                                <p class="admin-copy mt-3">{{ $feature['copy'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Gerer les produits</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Produits leaders et acces rapides</h2>
                </div>

                <a href="{{ route('dashboard.products') }}" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Ouvrir la gestion produit complete
                </a>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($productLeaders as $product)
                    <article class="admin-link-card rounded-[1.6rem] border border-[var(--brand-line)] bg-white/80 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="admin-kicker">Produit {{ $product['code'] }}</p>
                                <h3 class="mt-3 text-xl font-semibold text-[var(--brand-ink)]">{{ $product['name'] }}</h3>
                                <p class="admin-copy mt-3">{{ $product['category'] }}</p>
                            </div>
                            <span class="admin-pill {{ $product['visible'] ? 'is-active' : '' }}">
                                {{ $product['visible'] ? 'Visible' : 'Masque' }}
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                            <div class="rounded-[1rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-3">
                                <p class="admin-stat-label">Precos</p>
                                <p class="mt-2 text-lg font-semibold text-[var(--brand-ink)]">{{ $product['preorders'] }}</p>
                            </div>
                            <div class="rounded-[1rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-3">
                                <p class="admin-stat-label">Avis</p>
                                <p class="mt-2 text-lg font-semibold text-[var(--brand-ink)]">{{ $product['reviews'] }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ $product['dashboardUrl'] }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                                Editer
                            </a>
                            <a href="{{ $product['publicUrl'] }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                                Voir la fiche
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="study-report" class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Rapport d'etude</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Analyse des {{ $study['days'] }} derniers jours pour suivre les signaux du site</h2>
                </div>

                <div class="flex flex-wrap gap-3">
                    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-3">
                        <label for="study_days" class="admin-field-label !mb-0">Periode</label>
                        <select id="study_days" name="study_days" class="admin-select min-w-[11rem]">
                            @foreach ([14, 30, 60, 90, 180] as $days)
                                <option value="{{ $days }}" @selected($study['days'] === $days)>{{ $days }} jours</option>
                            @endforeach
                        </select>
                        <button type="submit" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                            Actualiser
                        </button>
                    </form>

                    <a href="{{ route('admin.reports.study.export', ['days' => $study['days']]) }}" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                        Export CSV
                    </a>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Precommandes</p>
                    <p class="admin-stat-value mt-4">{{ $study['totals']['preorders'] }}</p>
                    <p class="admin-stat-copy mt-3">Demandes WhatsApp suivies entre le {{ $study['dateStart'] }} et le {{ $study['dateEnd'] }}.</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Avis</p>
                    <p class="admin-stat-value mt-4">{{ $study['totals']['reviews'] }}</p>
                    <p class="admin-stat-copy mt-3">Commentaires laisses sur les fiches produits pendant la periode.</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Precommandes / jour</p>
                    <p class="admin-stat-value mt-4">{{ number_format($study['totals']['preordersPerDay'], 1) }}</p>
                    <p class="admin-stat-copy mt-3">Moyenne quotidienne sur la periode observee.</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Tendance precommandes 7j</p>
                    <p class="admin-stat-value mt-4">{{ number_format($study['totals']['preorderTrend7d'], 1) }}%</p>
                    <p class="admin-stat-copy mt-3">Variation du volume de precommandes entre les 7 derniers jours et les 7 jours precedents.</p>
                </article>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="overflow-hidden rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                    <div class="border-b border-[var(--brand-line)] px-5 py-4">
                        <h3 class="text-lg font-semibold text-[var(--brand-ink)]">Chronologie recente</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-[var(--brand-panel)] text-[var(--brand-teal-soft)]">
                                <tr>
                                    <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Date</th>
                                    <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Precommandes</th>
                                    <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Avis</th>
                                    <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">A/B</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (collect($study['daily'])->take(-10)->reverse() as $row)
                                    <tr class="border-t border-[var(--brand-line)]">
                                        <td class="px-4 py-3 font-semibold text-[var(--brand-ink)]">{{ $row['date'] }}</td>
                                        <td class="px-4 py-3">{{ $row['preorders'] }}</td>
                                        <td class="px-4 py-3">{{ $row['reviews'] }}</td>
                                        <td class="px-4 py-3 text-[var(--brand-copy)]">
                                            Precos A/B: {{ $row['preorders_a'] }}/{{ $row['preorders_b'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overflow-hidden rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                    <div class="border-b border-[var(--brand-line)] px-5 py-4">
                        <h3 class="text-lg font-semibold text-[var(--brand-ink)]">Performance produit</h3>
                    </div>
                    <div class="divide-y divide-[var(--brand-line)]">
                        @foreach (array_slice($study['products'], 0, 6) as $product)
                            <article class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="admin-kicker">Produit {{ $product['code'] }}</p>
                                        <h4 class="mt-2 text-lg font-semibold text-[var(--brand-ink)]">{{ $product['name'] }}</h4>
                                    </div>
                                    <a href="{{ $product['publicUrl'] }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em]">
                                        Fiche
                                    </a>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-[1rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-3">
                                        <p class="admin-stat-label">Precos</p>
                                        <p class="mt-2 text-lg font-semibold text-[var(--brand-ink)]">{{ $product['preorders'] }}</p>
                                    </div>
                                    <div class="rounded-[1rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-3">
                                        <p class="admin-stat-label">Avis</p>
                                        <p class="mt-2 text-lg font-semibold text-[var(--brand-ink)]">{{ $product['reviews'] }}</p>
                                    </div>
                                </div>
                                <p class="admin-copy mt-4">
                                    Part des precommandes: {{ number_format($product['preorderSharePercent'], 1) }} % sur la periode etudiee.
                                </p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="orders" class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Commandes</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Pilotage des paiements et du chiffre</h2>
                </div>

                <p class="admin-copy max-w-3xl">
                    Le checkout est branche sur FedaPay et le dashboard vous remonte ici les flux les plus recents pour suivre la sante commerciale du site.
                </p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-4">
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Total commandes</p>
                    <p class="admin-stat-value mt-4">{{ $orderOverview['total'] }}</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">En attente</p>
                    <p class="admin-stat-value mt-4">{{ $orderOverview['pending'] }}</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">Paiements valides</p>
                    <p class="admin-stat-value mt-4">{{ $orderOverview['paid'] }}</p>
                </article>
                <article class="admin-stat-card">
                    <p class="admin-stat-label">CA encaisse</p>
                    <p class="admin-stat-value mt-4">{{ number_format($orderOverview['revenue'], 0, ',', ' ') }} FCFA</p>
                </article>
            </div>

            <div class="mt-8 overflow-hidden rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[var(--brand-panel)] text-[var(--brand-teal-soft)]">
                            <tr>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Reference</th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Produit</th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Client</th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Montant</th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.12em]">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr class="border-t border-[var(--brand-line)]">
                                    <td class="px-4 py-3 font-semibold text-[var(--brand-ink)]">{{ $order->reference }}</td>
                                    <td class="px-4 py-3">{{ $order->product?->name ?? 'Produit supprime' }}</td>
                                    <td class="px-4 py-3">{{ trim($order->customer_first_name.' '.$order->customer_last_name) }}</td>
                                    <td class="px-4 py-3">{{ $order->formattedAmount() }}</td>
                                    <td class="px-4 py-3">
                                        <span class="admin-pill {{ in_array($order->status, ['approved', 'transferred'], true) ? 'is-active' : '' }}">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-5 text-[var(--brand-copy)]">Aucune commande enregistree pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="engagement" class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Engagement</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Newsletter, campagnes flash et fidelisation</h2>
                </div>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <div class="space-y-6">
                    <section class="rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                        <p class="admin-kicker">Newsletter</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <article class="admin-stat-card">
                                <p class="admin-stat-label">Inscrits</p>
                                <p class="admin-stat-value mt-4">{{ $newsletterOverview['subscribers'] }}</p>
                                <p class="admin-stat-copy mt-3">{{ $newsletterOverview['recentSubscribers'] }} sur les 7 derniers jours.</p>
                            </article>
                            <article class="admin-stat-card">
                                <p class="admin-stat-label">Conversions</p>
                                <p class="admin-stat-value mt-4">{{ $newsletterOverview['conversions'] }}</p>
                                <p class="admin-stat-copy mt-3">
                                    {{ number_format($newsletterOverview['conversionRate'], 1) }} % pour {{ number_format($newsletterOverview['revenue'], 0, ',', ' ') }} FCFA.
                                </p>
                            </article>
                        </div>

                        <div class="mt-5 rounded-[1.25rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-4">
                            <h3 class="text-base font-semibold text-[var(--brand-ink)]">Top sources d'inscription</h3>
                            <div class="mt-4 space-y-3">
                                @forelse ($newsletterOverview['topSources'] as $source)
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-sm text-[var(--brand-copy)]">{{ $source['label'] }}</span>
                                        <span class="admin-pill">{{ $source['total'] }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-[var(--brand-copy)]">Aucune source newsletter enregistree pour le moment.</p>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                        <p class="admin-kicker">Notifications navigateur</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-3">
                            <article class="admin-stat-card">
                                <p class="admin-stat-label">Granted</p>
                                <p class="admin-stat-value mt-4">{{ $browserNotificationOverview['granted'] }}</p>
                            </article>
                            <article class="admin-stat-card">
                                <p class="admin-stat-label">Denied</p>
                                <p class="admin-stat-value mt-4">{{ $browserNotificationOverview['denied'] }}</p>
                            </article>
                            <article class="admin-stat-card">
                                <p class="admin-stat-label">Default</p>
                                <p class="admin-stat-value mt-4">{{ $browserNotificationOverview['default'] }}</p>
                            </article>
                        </div>
                    </section>

                    <section class="rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                        <p class="admin-kicker">Loyaute</p>
                        <div class="mt-4 space-y-4">
                            @forelse ($loyaltyLeaderboard as $member)
                                <article class="rounded-[1.25rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-semibold text-[var(--brand-ink)]">{{ $member['name'] }}</h3>
                                            <p class="admin-copy mt-2">{{ $member['email'] }}</p>
                                        </div>
                                        <span class="admin-pill is-active">{{ $member['tier'] }}</span>
                                    </div>
                                    <p class="admin-copy mt-3">{{ $member['points'] }} points cumules.</p>
                                </article>
                            @empty
                                <p class="text-sm text-[var(--brand-copy)]">Aucun membre fidelite classe pour le moment.</p>
                            @endforelse
                        </div>
                    </section>
                </div>

                <section class="rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                    <div class="admin-page-header">
                        <div>
                            <p class="admin-kicker">Campagne flash</p>
                            <h3 class="mt-3 text-xl font-semibold text-[var(--brand-ink)]">Creer une campagne navigateur</h3>
                        </div>
                        <span class="admin-pill">{{ $flashCampaigns->count() }} recente(s)</span>
                    </div>

                    <form method="POST" action="{{ route('admin.flash-campaigns.store') }}" class="mt-6 space-y-5">
                        @csrf

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="campaign-title" class="admin-field-label">Titre</label>
                                <input id="campaign-title" name="title" type="text" value="{{ old('title') }}" class="admin-input">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="campaign-message" class="admin-field-label">Message</label>
                                <textarea id="campaign-message" name="message" rows="3" class="admin-textarea">{{ old('message') }}</textarea>
                            </div>

                            <div>
                                <label for="discount_code" class="admin-field-label">Code promo</label>
                                <input id="discount_code" name="discount_code" type="text" value="{{ old('discount_code') }}" class="admin-input">
                            </div>

                            <div>
                                <label for="audience" class="admin-field-label">Audience</label>
                                <select id="audience" name="audience" class="admin-select">
                                    @foreach (['all' => 'Tous', 'newsletter' => 'Newsletter', 'loyalty' => 'Loyaute'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('audience', 'all') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="cta_label" class="admin-field-label">Label CTA</label>
                                <input id="cta_label" name="cta_label" type="text" value="{{ old('cta_label') }}" class="admin-input">
                            </div>

                            <div>
                                <label for="cta_url" class="admin-field-label">URL CTA</label>
                                <input id="cta_url" name="cta_url" type="url" value="{{ old('cta_url', route('catalog.index')) }}" class="admin-input">
                            </div>

                            <div>
                                <label for="starts_at" class="admin-field-label">Debut</label>
                                <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" class="admin-input">
                            </div>

                            <div>
                                <label for="ends_at" class="admin-field-label">Fin</label>
                                <input id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}" class="admin-input">
                            </div>
                        </div>

                        <label class="inline-flex items-center gap-3 text-sm font-medium text-[var(--brand-copy)]">
                            <input name="is_active" type="checkbox" value="1" class="h-4 w-4" @checked(old('is_active', true))>
                            Activer immediatement la campagne
                        </label>

                        <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                            Enregistrer la campagne
                        </button>
                    </form>

                    <div class="mt-8 space-y-4">
                        @forelse ($flashCampaigns as $campaign)
                            <article class="rounded-[1.25rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h4 class="text-lg font-semibold text-[var(--brand-ink)]">{{ $campaign->title }}</h4>
                                        <p class="admin-copy mt-2">{{ $campaign->message }}</p>
                                    </div>
                                    <span class="admin-pill {{ $campaign->is_active ? 'is-active' : '' }}">
                                        {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <p class="admin-copy mt-3">
                                    Audience: {{ $campaign->audience }}. Impressions: {{ $campaign->impressions_count }}.
                                </p>
                            </article>
                        @empty
                            <div class="admin-empty-state">
                                Aucune campagne flash n'a encore ete creee.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>

        <section id="settings" class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">WhatsApp</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Reglages critiques du parcours d'achat</h2>
                </div>

                <p class="admin-copy max-w-3xl">
                    Cette zone controle le numero WhatsApp utilise pour les precommandes trackees depuis le site.
                </p>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
                <div class="grid gap-4">
                    <article class="admin-stat-card">
                        <p class="admin-stat-label">WhatsApp actuel</p>
                        <p class="admin-stat-value mt-4">{{ $settings['whatsapp_number'] ?: 'Non defini' }}</p>
                        <p class="admin-stat-copy mt-3">Ce numero recoit les demandes de precommande depuis le site.</p>
                    </article>
                </div>

                <form method="POST" action="{{ route('admin.settings.whatsapp') }}" class="space-y-5 rounded-[1.75rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="whatsapp_number" class="admin-field-label">Numero WhatsApp</label>
                        <input
                            id="whatsapp_number"
                            name="whatsapp_number"
                            type="text"
                            value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}"
                            class="admin-input"
                        >
                        <p class="admin-helper mt-2">Le systeme nettoie automatiquement le numero et le stocke au format numerique.</p>
                    </div>

                    <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                        Enregistrer les reglages
                    </button>
                </form>
            </div>
        </section>

        <section id="reviews" class="admin-surface px-6 py-6 sm:px-8">
            <div class="admin-page-header">
                <div>
                    <p class="admin-kicker">Avis recents</p>
                    <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Moderation des commentaires visibles sur les fiches</h2>
                </div>
            </div>

            <div class="mt-8 grid gap-4 xl:grid-cols-2">
                @forelse ($recentReviews as $review)
                    <article class="admin-review-card rounded-[1.6rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="admin-kicker">{{ $review->product?->code ? 'Produit '.$review->product->code : 'Avis' }}</p>
                                <h3 class="mt-3 text-lg font-semibold text-[var(--brand-ink)]">{{ $review->author_name }}</h3>
                                <p class="admin-copy mt-2">{{ $review->product?->name ?? 'Produit non relie' }}</p>
                            </div>
                            <span class="admin-pill is-active">{{ $review->rating }}/5</span>
                        </div>

                        <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">{{ $review->body }}</p>

                        <div class="mt-5 flex flex-wrap gap-3">
                            @if ($review->product)
                                <a href="{{ route('products.show', $review->product) }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.14em]">
                                    Voir la fiche
                                </a>
                            @endif

                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Supprimer cet avis ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center rounded-[999px] border border-[rgba(176,73,68,0.22)] bg-[rgba(255,241,239,0.8)] px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.12em] text-[#8b3733] transition hover:bg-[rgba(255,232,230,0.95)]">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="admin-empty-state xl:col-span-2">
                        Aucun avis recent a moderer.
                    </div>
                @endforelse
            </div>
        </section>

        <section id="support" class="admin-surface px-6 py-6 sm:px-8">
                <div class="admin-page-header">
                    <div>
                        <p class="admin-kicker">Support</p>
                        <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Conversations a reprendre ou a fermer</h2>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($supportConversations as $conversation)
                        <article class="rounded-[1.6rem] border border-[var(--brand-line)] bg-white/80 p-5 shadow-[0_18px_45px_rgba(8,38,45,0.06)]">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="admin-kicker">{{ $conversation->product?->code ? 'Produit '.$conversation->product->code : 'Conversation generale' }}</p>
                                    <h3 class="mt-3 text-lg font-semibold text-[var(--brand-ink)]">{{ $conversation->customer_name ?: 'Visiteur anonyme' }}</h3>
                                    <p class="admin-copy mt-2">
                                        {{ $conversation->customer_email ?: 'Email non renseigne' }}<br>
                                        Source: {{ $conversation->source_path ?: 'widget support' }}
                                    </p>
                                </div>
                                <span class="admin-pill {{ $conversation->status === 'resolved' ? 'is-active' : '' }}">{{ $conversation->status }}</span>
                            </div>

                            <div class="mt-5 space-y-3">
                                @foreach ($conversation->messages->take(-2) as $message)
                                    <div class="rounded-[1rem] border border-[var(--brand-line)] bg-[var(--brand-panel)] p-3">
                                        <p class="admin-stat-label">{{ $message->role }}</p>
                                        <p class="mt-2 text-sm leading-6 text-[var(--brand-copy)]">{{ $message->body }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <form method="POST" action="{{ route('admin.support.update', $conversation) }}" class="mt-5 flex flex-wrap items-end gap-3">
                                @csrf
                                @method('PUT')

                                <div class="min-w-[14rem] flex-1">
                                    <label for="support-status-{{ $conversation->id }}" class="admin-field-label">Statut</label>
                                    <select id="support-status-{{ $conversation->id }}" name="status" class="admin-select">
                                        @foreach (['open' => 'Open', 'needs-human' => 'Needs human', 'resolved' => 'Resolved'] as $value => $label)
                                            <option value="{{ $value }}" @selected($conversation->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                    Mettre a jour
                                </button>
                            </form>
                        </article>
                    @empty
                        <div class="admin-empty-state">
                            Aucune conversation support n'attend d'action.
                        </div>
                    @endforelse
                </div>
        </section>
    </div>
</x-app-layout>
