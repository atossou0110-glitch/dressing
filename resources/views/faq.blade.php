<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    @include('partials.fixed-viewport')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FAQ - King Rangement Benin</title>
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
                <a href="{{ route('catalog.index') }}" class="brand-nav-link">Accueil</a>
                <a href="{{ route('catalog.index') }}#rangement" class="brand-nav-link">Rangements</a>
                <a href="{{ route('catalog.index') }}#dressing" class="brand-nav-link">Dressing</a>
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
                <a href="{{ route('catalog.index') }}" class="brand-mobile-menu-link">Accueil</a>
                <a href="{{ route('catalog.index') }}#rangement" class="brand-mobile-menu-link">Rangements</a>
                <a href="{{ route('catalog.index') }}#dressing" class="brand-mobile-menu-link">Dressing</a>
                <a href="{{ route('catalog.dr-dressing') }}" class="brand-mobile-menu-link">Solutions King</a>
                <a href="{{ route('faq') }}" class="brand-mobile-menu-link">FAQ</a>
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
    <main class="brand-main">
        <!-- FAQ Content Section -->
        <section class="brand-section-light py-12" id="faq-content">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-10 max-w-3xl" data-reveal>
                        <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x2139;&#xFE0F;</span> Besoin d'aide</p>
                        <h1 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">Questions fréquemment posées</h1>
                        <p class="mt-4 text-base leading-7 text-[var(--brand-copy)]">
                            Trouvez rapidement les réponses les plus utiles sur les produits, la livraison, les retours et l'accompagnement.
                        </p>
                    </div>

                    <div class="storefront-fixed-grid-2 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="space-y-3">
                            <h2 class="mb-4 text-lg font-semibold text-[var(--brand-ink)]">Sur les produits</h2>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Quelles sont les dimensions de chaque meuble ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Toutes les dimensions sont indiquées sur la fiche produit. Vous pouvez aussi utiliser notre outil de comparaison pour vérifier la compatibilité avec votre espace.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Les produits sont-ils modulables ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Nos dressings et armoires peuvent être ajustés selon les configurations proposées. Les options disponibles sont détaillées sur chaque fiche.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>En quels matériaux sont faits les meubles ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Les références sont fabriquées en bois massif ou en panneaux de qualité supérieure, selon le modèle et le positionnement.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Quel poids maximum peut supporter un meuble ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    La capacité de charge est précisée pour chaque étagère, tiroir ou compartiment dans la partie spécifications.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Les couleurs sont-elles fidèles sur le site ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Nous travaillons des photos lumineuses et naturelles, mais un léger écart peut apparaître selon l'écran ou l'éclairage de votre pièce.
                                </p>
                            </details>
                        </div>

                        <div class="space-y-3">
                            <h2 class="mb-4 text-lg font-semibold text-[var(--brand-ink)]">Livraison et service</h2>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Combien de temps pour la livraison ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    La livraison standard se fait en 5 à 7 jours ouvrés. Une option plus rapide peut être proposée selon la zone.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>La livraison est-elle gratuite ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Elle est offerte à partir de 500 000 FCFA. En dessous, les frais dépendent du volume de la commande et de la localisation.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Livrez-vous en région ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Oui. Nous livrons selon les zones couvertes avec une estimation ajustée au panier et lors de la confirmation.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Puis-je retourner ma commande ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Vous disposez de 30 jours pour le retour d'un produit non utilisé. En cas de problème de fabrication, la prise en charge est prioritaire.
                                </p>
                            </details>

                            <details class="group brand-faq-item p-5" data-reveal>
                                <summary class="brand-faq-summary font-semibold text-[var(--brand-ink)]">
                                    <span>Offrez-vous le service de montage ?</span>
                                    <span class="text-[var(--brand-sand)] transition group-open:rotate-180">v</span>
                                </summary>
                                <p class="brand-faq-answer text-sm leading-7 text-[var(--brand-copy)]">
                                    Un service d'assemblage peut être ajouté selon les produits. Il est proposé comme option au moment de l'achat.
                                </p>
                            </details>
                        </div>
                    </div>

                    <div class="mt-10 brand-support-cta p-8 text-center" data-reveal>
                        <p class="text-[var(--brand-copy)]">Vous n'avez pas trouvé votre réponse ?</p>
                        <h3 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Notre équipe peut vous orienter</h3>
                        <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-[var(--brand-copy)]">
                            Contactez-nous pour une question sur un produit, une commande ou une recommandation d'aménagement.
                        </p>
                        <div class="storefront-fixed-row storefront-fixed-center mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                            <a href="mailto:contact@kingrangement.bj" class="brand-button-primary inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                Envoyer un email
                            </a>
                            @if (! empty($whatsAppSupportUrl))
                                <a href="{{ $whatsAppSupportUrl }}" target="_blank" rel="noopener" aria-label="Ouvrir WhatsApp" title="WhatsApp" class="brand-button-whatsapp inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                    <span class="brand-whatsapp-icon" aria-hidden="true">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                            <path d="M12.04 3.25a8.62 8.62 0 0 0-7.43 13l-1.03 3.86 3.95-1.01a8.63 8.63 0 1 0 4.51-15.85Z" fill="currentColor" opacity=".22"/>
                                            <path d="M12.04 4.64a7.23 7.23 0 0 0-6.18 10.98l.18.29-.58 2.19 2.24-.57.28.17a7.24 7.24 0 1 0 4.06-13.06Zm4.1 10.48c-.17.48-.96.9-1.33.96-.35.05-.8.08-1.29-.08-.3-.1-.68-.22-1.17-.43-2.05-.88-3.39-2.92-3.5-3.06-.1-.13-.84-1.11-.84-2.12 0-1 .53-1.5.72-1.7.18-.2.4-.25.54-.25h.39c.12 0 .29-.05.45.35.17.4.57 1.38.62 1.48.05.1.08.23.02.36-.06.13-.1.22-.2.34-.1.12-.2.26-.3.35-.1.1-.2.2-.09.4.12.2.51.84 1.1 1.36.75.67 1.39.88 1.59.98.2.1.32.08.44-.05.12-.14.5-.59.64-.79.13-.2.27-.16.45-.1.19.07 1.18.56 1.38.66.2.1.34.15.39.24.05.08.05.48-.11.95Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <span>WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    </div>

    <footer class="brand-footer py-8">
        @include('partials.social-links')
        
        <div class="storefront-fixed-footer mx-auto flex max-w-7xl flex-col gap-4 px-4 text-sm text-[var(--brand-sand-soft)] sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="flex items-center gap-4">
                <x-brand-logo class="brand-logo-header" compact />
                <p>&copy; 2026 King Rangement Benin. Catalogue meuble avec panier, filtres, commentaires et fiches produit enrichies.</p>
            </div>
            <div class="flex flex-wrap gap-5">
                <a href="{{ route('catalog.index') }}" class="brand-nav-link">Accueil</a>
                <a href="{{ route('catalog.index') }}#rangement" class="brand-nav-link">Rangements</a>
                <a href="{{ route('catalog.index') }}#dressing" class="brand-nav-link">Dressing</a>
                <a href="{{ route('faq') }}" class="brand-nav-link">FAQ</a>
            </div>
        </div>
    </footer>

    @include('partials.newsletter-popup')

    @include('partials.recent-products')

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease-out forwards;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.Dressingue?.initCatalog?.();
        });
    </script>
</body>
</html>
