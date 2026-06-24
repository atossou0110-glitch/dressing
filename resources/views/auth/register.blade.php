<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center sm:text-left">
            <p class="admin-kicker">Nou vel accès</p>
            <h1 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Inscription</h1>
            <p class="admin-copy mt-3">
            Creez un compte King Rangement Benin. Les comptes non admin restent limites aux parcours autorises.
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" value="Mot de passe" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="admin-empty-state">
                Utilisez un mot de passe solide pour protéger l'accès à l'espace admin.
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <a class="text-sm font-semibold text-[var(--brand-sand-dark)] transition hover:text-[var(--brand-ink)]" href="{{ route('login') }}">
                    Déjà inscrit ?
                </a>

                <x-primary-button>
                    Inscription
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

