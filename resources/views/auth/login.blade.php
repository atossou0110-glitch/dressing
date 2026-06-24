<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center sm:text-left">
            <p class="admin-kicker">Administration</p>
            <h1 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Connexion</h1>
            <p class="admin-copy mt-3">
            Connectez-vous pour acceder au tableau de bord King Rangement Benin et gerer les vues, les produits et les parametres.
            </p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" value="Mot de passe" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-[var(--brand-copy)]">
                    <input id="remember_me" type="checkbox" class="rounded border-[rgba(10,49,56,0.18)] bg-white text-[var(--brand-sand-dark)] focus:ring-[rgba(216,176,120,0.35)]" name="remember">
                    <span>Se souvenir de moi</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-[var(--brand-sand-dark)] transition hover:text-[var(--brand-ink)]" href="{{ route('password.request') }}">
                        Mot de passe oublie ?
                    </a>
                @endif
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('catalog.index') }}" class="text-sm text-[var(--brand-copy)] transition hover:text-[var(--brand-ink)]">
                    Retour au site
                </a>

                <x-primary-button>
                    Connexion
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
