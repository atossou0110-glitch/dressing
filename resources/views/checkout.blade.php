<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    @include('partials.fixed-viewport')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Commande | King Rangement Benin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="brand-body storefront-fixed-body min-h-screen antialiased">
    <header class="brand-header sticky top-0 z-50">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-2.5 px-4 py-2 sm:px-6 lg:px-8">
            <a href="{{ route('catalog.index') }}" class="shrink-0">
                <x-brand-logo class="brand-logo-header" />
            </a>

            <a href="{{ route('catalog.index') }}" class="brand-header-button px-2.5 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em]">
                Retour
            </a>
        </div>
    </header>

    <div class="storefront-fixed-frame" data-storefront-fixed-frame>
    <div class="storefront-fixed-stage" data-storefront-fixed-stage data-storefront-stage-width="1280">
    <main class="brand-page-enter">
        <section class="brand-section-light py-12">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10">
                    <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">🛒</span> Finaliser commande</p>
                    <h1 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">
                        Vos informations de commande
                    </h1>
                </div>

                <form action="{{ route('products.checkout.store', ['product' => $product]) }}" method="POST" class="space-y-5 brand-card p-6 sm:p-8">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-500/30 bg-red-500/10 p-4 mb-6">
                            <p class="mb-2 text-sm font-semibold text-red-500">Erreurs:</p>
                            <ul class="list-inside space-y-1 text-sm text-red-500">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="customer_first_name" class="block text-sm font-semibold text-[var(--brand-ink)]">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="customer_first_name" 
                                id="customer_first_name"
                                placeholder="Votre prénom"
                                required
                                value="{{ old('customer_first_name') }}"
                                class="mt-2 w-full rounded-lg border border-[rgba(211,176,130,0.2)] bg-[rgba(255,255,255,0.05)] px-4 py-2.5 text-sm text-[var(--brand-sand)] placeholder-[rgba(211,176,130,0.4)] transition focus:border-[rgba(211,176,130,0.4)] focus:outline-none focus:ring-1 focus:ring-[rgba(211,176,130,0.2)]"
                            >
                        </div>

                        <div>
                            <label for="customer_last_name" class="block text-sm font-semibold text-[var(--brand-ink)]">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="customer_last_name" 
                                id="customer_last_name"
                                placeholder="Votre nom"
                                required
                                value="{{ old('customer_last_name') }}"
                                class="mt-2 w-full rounded-lg border border-[rgba(211,176,130,0.2)] bg-[rgba(255,255,255,0.05)] px-4 py-2.5 text-sm text-[var(--brand-sand)] placeholder-[rgba(211,176,130,0.4)] transition focus:border-[rgba(211,176,130,0.4)] focus:outline-none focus:ring-1 focus:ring-[rgba(211,176,130,0.2)]"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-sm font-semibold text-[var(--brand-ink)]">
                            Téléphone <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            name="customer_phone" 
                            id="customer_phone"
                            placeholder="+229 XX XX XX XX"
                            required
                            value="{{ old('customer_phone') }}"
                            class="mt-2 w-full rounded-lg border border-[rgba(211,176,130,0.2)] bg-[rgba(255,255,255,0.05)] px-4 py-2.5 text-sm text-[var(--brand-sand)] placeholder-[rgba(211,176,130,0.4)] transition focus:border-[rgba(211,176,130,0.4)] focus:outline-none focus:ring-1 focus:ring-[rgba(211,176,130,0.2)]"
                        >
                    </div>

                    <div>
                        <label for="customer_address" class="block text-sm font-semibold text-[var(--brand-ink)]">
                            Adresse <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="customer_address"
                            id="customer_address"
                            placeholder="Votre adresse"
                            required
                            value="{{ old('customer_address') }}"
                            class="mt-2 w-full rounded-lg border border-[rgba(211,176,130,0.2)] bg-[rgba(255,255,255,0.05)] px-4 py-2.5 text-sm text-[var(--brand-sand)] placeholder-[rgba(211,176,130,0.4)] transition focus:border-[rgba(211,176,130,0.4)] focus:outline-none focus:ring-1 focus:ring-[rgba(211,176,130,0.2)]"
                        >
                    </div>

                    @if ($fedapayConfigured)
                        <div>
                            <label for="payment_method" class="block text-sm font-semibold text-[var(--brand-ink)]">
                                Méthode de paiement <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="payment_method" 
                                id="payment_method"
                                required
                                class="mt-2 w-full rounded-lg border border-[rgba(211,176,130,0.2)] bg-[rgba(255,255,255,0.05)] px-4 py-2.5 text-sm text-[var(--brand-sand)] transition focus:border-[rgba(211,176,130,0.4)] focus:outline-none focus:ring-1 focus:ring-[rgba(211,176,130,0.2)]"
                            >
                                <option value="{{ $defaultPaymentMethod }}" selected>{{ $paymentMethod['label'] }}</option>
                            </select>
                            <p class="mt-2 text-xs text-[var(--brand-copy)]">
                                Redirection securisee vers FedaPay apres validation du formulaire.
                            </p>
                        </div>

                        <button 
                            type="submit"
                            class="brand-button-primary w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]"
                        >
                            Payer avec FedaPay
                        </button>
                    @else
                        <div class="rounded-lg border border-red-500/30 bg-red-500/10 p-4">
                            <p class="text-sm text-red-500">
                                Le paiement en ligne n'est pas disponible pour le moment. Veuillez contacter le service client.
                            </p>
                        </div>
                    @endif
                </form>
            </div>
        </section>
    </main>

    <footer class="brand-footer border-t border-[rgba(211,176,130,0.18)] py-12">
        @include('partials.social-links')
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-[var(--brand-copy)]">
                <p>© 2026 King Rangement Benin. Tous droits reserves.</p>
            </div>
        </div>
    </footer>
    </div>
    </div>
</body>
</html>
