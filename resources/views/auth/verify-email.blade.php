<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center sm:text-left">
            <p class="admin-kicker">Vérification</p>
            <h1 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Confirmez votre email</h1>
            <p class="admin-copy mt-3">
                Vérifiez votre adresse email via le lien envoyé. Si besoin, vous pouvez demander un nouvel envoi.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="admin-status admin-status-success">
                Un nouveau lien de vérification a été envoyé à votre adresse email.
            </div>
        @endif

        <div class="admin-empty-state">
            Tant que l'email n'est pas confirme, certaines actions de l'administration peuvent rester bloquees.
        </div>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button>
                    Renvoyer le lien
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-[var(--brand-copy)] transition hover:text-[var(--brand-ink)]">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

