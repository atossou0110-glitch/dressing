<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        @include('partials.fixed-viewport')
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Acces securise | King Rangement Benin</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="brand-body admin-shell min-h-screen antialiased">
        <div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-7xl items-center justify-center">
                <div class="grid w-full gap-8 xl:grid-cols-[1.05fr_0.95fr]">
                    <section class="admin-surface admin-surface-muted hidden px-8 py-10 lg:flex lg:flex-col lg:justify-between xl:px-10">
                        <div class="space-y-8">
                            <a href="{{ route('catalog.index') }}" class="inline-flex">
                                <x-application-logo class="h-24 w-auto" />
                            </a>

                            <div class="space-y-4">
                                <p class="admin-kicker">Espace securise</p>
                <h1 class="admin-title">Administration King Rangement Benin</h1>
                                <p class="admin-copy max-w-xl">
                                    Accedez a l'espace de gestion dans une interface coherente avec le storefront pour piloter le catalogue, les produits et les parametrages sensibles.
                                </p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <article class="admin-stat-card">
                                    <p class="admin-stat-label">Accès rapide</p>
                                    <p class="mt-4 text-lg font-semibold text-[var(--brand-ink)]">Catalogue public</p>
                                    <p class="admin-stat-copy mt-3">Revenir aux vues clients pour verifier le rendu en direct.</p>
                                </article>

                                <article class="admin-stat-card">
                                    <p class="admin-stat-label">Protection</p>
                                    <p class="mt-4 text-lg font-semibold text-[var(--brand-ink)]">Session securisee</p>
                                    <p class="admin-stat-copy mt-3">Les écrans d'accès utilisent les mêmes bases visuelles que le dashboard.</p>
                                </article>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('catalog.index') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                Voir le site
                            </a>

                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                                    Connexion
                                </a>
                            @endif
                        </div>
                    </section>

                    <section class="flex items-center">
                        <div class="w-full">
                            <div class="mb-6 flex justify-center lg:hidden">
                                <a href="{{ route('catalog.index') }}" class="inline-flex">
                                    <x-application-logo class="h-20 w-auto" />
                                </a>
                            </div>

                            <div class="admin-surface w-full px-6 py-7 sm:mx-auto sm:max-w-xl sm:px-8 sm:py-8">
                                <div class="mb-6 flex items-center justify-between gap-3 border-b border-[rgba(10,49,56,0.08)] pb-5">
                                    <div>
                                        <p class="admin-kicker">Accès</p>
                                        <p class="mt-2 text-sm text-[var(--brand-copy)]">Interface reservee a l'administration et aux comptes securises.</p>
                                    </div>

                                    <a href="{{ route('catalog.index') }}" class="brand-header-button px-3 py-2 text-[0.72rem] font-semibold uppercase tracking-[0.12em]">
                                        Retour site
                                    </a>
                                </div>

                                {{ $slot }}
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
