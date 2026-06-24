<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center sm:text-left">
            <p class="admin-kicker">Récupération</p>
            <h1 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Mot de passe oublie</h1>
            <p class="admin-copy mt-3">
                Indiquez votre adresse email et nous vous enverrons un lien de reinitialisation securise.
            </p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="admin-empty-state">
                Le lien de reinitialisation sera envoye a l'adresse associee a votre compte.
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('login') }}" class="text-sm text-[var(--brand-copy)] transition hover:text-[var(--brand-ink)]">
                    Retour a la connexion
                </a>

                <x-primary-button>
                    Envoyer le lien
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
