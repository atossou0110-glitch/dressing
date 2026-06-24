<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    @include('partials.fixed-viewport')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Suivi de commande | King Rangement Benin</title>
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
                    <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">📦</span> Suivi de commande</p>
                    <h1 class="brand-display mt-3 text-3xl text-[var(--brand-ink)] sm:text-4xl">
                        Suivi de votre commande
                    </h1>
                </div>

                <div class="brand-card p-6 sm:p-8 space-y-8">
                    <!-- Numéro de référence -->
                    <div class="border-b border-[rgba(211,176,130,0.18)] pb-6">
                        <p class="text-sm text-[var(--brand-copy)]">Référence de commande</p>
                        <p class="mt-2 text-2xl font-semibold text-[var(--brand-sand)]">
                            {{ $order->reference }}
                        </p>
                        <p class="mt-2 text-xs text-[var(--brand-copy)]">
                            Commande du {{ $order->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    <!-- Statut de la commande -->
                    <div class="border-b border-[rgba(211,176,130,0.18)] pb-6">
                        <p class="text-sm text-[var(--brand-copy)]">Statut</p>
                        <div class="mt-4 flex items-center gap-3">
                            @if ($order->isPaid())
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500/20">
                                    <span class="text-lg">✓</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-green-500">Paiement reçu</p>
                                    <p class="text-xs text-[var(--brand-copy)]">Votre commande a été payée avec succès.</p>
                                </div>
                            @elseif ($order->status === 'pending')
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-500/20">
                                    <span class="text-lg">⏳</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-yellow-500">En attente de paiement</p>
                                    <p class="text-xs text-[var(--brand-copy)]">En attente de confirmation du paiement.</p>
                                </div>
                            @elseif ($order->status === 'failed')
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500/20">
                                    <span class="text-lg">✕</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-red-500">Paiement échoué</p>
                                    <p class="text-xs text-[var(--brand-copy)]">Le paiement n'a pas pu être traité.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informations du client -->
                    <div class="border-b border-[rgba(211,176,130,0.18)] pb-6">
                        <p class="text-sm text-[var(--brand-copy)]">Informations de contact</p>
                        <div class="mt-4 space-y-2 text-sm">
                            <p>
                                <span class="text-[var(--brand-copy)]">Nom:</span>
                                <span class="font-semibold text-[var(--brand-sand)]">{{ $order->customer_first_name }} {{ $order->customer_last_name }}</span>
                            </p>
                            <p>
                                <span class="text-[var(--brand-copy)]">Téléphone:</span>
                                <span class="font-semibold text-[var(--brand-sand)]">{{ $order->customer_phone }}</span>
                            </p>
                            @if ($order->customer_email)
                                <p>
                                    <span class="text-[var(--brand-copy)]">Email:</span>
                                    <span class="font-semibold text-[var(--brand-sand)]">{{ $order->customer_email }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Montant de la commande -->
                    <div class="border-b border-[rgba(211,176,130,0.18)] pb-6">
                        <p class="text-sm text-[var(--brand-copy)]">Montant</p>
                        <p class="mt-2 text-2xl font-semibold text-[var(--brand-sand)]">
                            {{ number_format($order->amount, 0, ',', ' ') }} {{ $order->currency }}
                        </p>
                    </div>

                    <!-- Métrique de paiement -->
                    @if ($order->isPaid())
                        <div class="rounded-lg border border-green-500/30 bg-green-500/10 p-4">
                            <p class="text-sm text-green-500">
                                Merci pour votre achat! Vous recevrez une confirmation par email.
                            </p>
                        </div>
                    @elseif ($shouldPoll)
                        <div class="rounded-lg border border-yellow-500/30 bg-yellow-500/10 p-4">
                            <p class="text-sm text-yellow-500">
                                Veuillez patienter, nous vérifions le statut de votre paiement...
                            </p>
                        </div>

                        <script>
                            let pollCount = 0;
                            const maxPolls = 120; // 2 minutes max

                            function checkOrderStatus() {
                                pollCount++;
                                if (pollCount > maxPolls) {
                                    console.log('Polling stopped after max attempts');
                                    return;
                                }

                                fetch('{{ $statusUrl }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.isPaid) {
                                            location.reload();
                                        } else if (pollCount < maxPolls) {
                                            setTimeout(checkOrderStatus, 2000); // Vérifier toutes les 2 secondes
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error checking status:', error);
                                        if (pollCount < maxPolls) {
                                            setTimeout(checkOrderStatus, 5000); // Réessayer après 5 secondes en cas d'erreur
                                        }
                                    });
                            }

                            // Commencer à vérifier le statut
                            checkOrderStatus();
                        </script>
                    @endif

                    <!-- Lien retour -->
                    <div class="flex gap-3 pt-4">
                        <a href="{{ route('catalog.index') }}" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
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
