<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        @include('partials.fixed-viewport')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Administration | King Rangement Benin</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="brand-body admin-shell min-h-screen antialiased">
        @php
            $user = auth()->user();
            $isAdmin = (bool) ($user?->is_admin);
        @endphp

        <div class="min-h-screen">
            <header class="brand-header admin-nav-shell sticky top-0 z-50" data-mobile-header>
                <div class="mx-auto flex max-w-7xl flex-nowrap items-center justify-between gap-4 overflow-hidden px-4 py-2.5 sm:px-6 lg:px-8">
                    <div class="flex shrink-0 items-center gap-4 lg:gap-6">
                        <a href="{{ route('catalog.index') }}" class="shrink-0">
                            <x-brand-logo class="brand-logo-header" />
                        </a>

                        @if ($isAdmin)
                            <nav class="flex min-w-0 items-center gap-2 whitespace-nowrap">
                                <a href="{{ route('dashboard') }}" class="admin-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                                    Vue d'ensemble
                                </a>
                                <a href="{{ route('dashboard.products') }}" class="admin-nav-link {{ request()->routeIs('dashboard.products*') ? 'is-active' : '' }}">
                                    Produits
                                </a>
                                <a href="{{ route('profile.edit') }}" class="admin-nav-link hidden sm:inline-flex {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                                    Profil
                                </a>
                            </nav>
                        @endif
                    </div>

                    <div class="flex shrink-0 items-center gap-2.5 sm:gap-3">
                        <a href="{{ route('catalog.index') }}" class="brand-header-button px-3 py-2 text-[0.72rem] font-semibold uppercase tracking-[0.12em]">
                            Voir le site
                        </a>

                        @if ($user)
                            <span class="hidden text-sm font-medium text-[var(--brand-copy)] sm:inline">{{ $user->name }}</span>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="brand-button-secondary px-4 py-2 text-[0.72rem] font-semibold uppercase tracking-[0.12em]">
                                    Déconnexion
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </header>

            @isset($header)
                <section class="admin-header-shell mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                    <div class="admin-surface px-6 py-6 sm:px-8 sm:py-7">
                        {{ $header }}
                    </div>
                </section>
            @endisset

            <main class="admin-page-shell">
                <div class="admin-page-inner mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>

