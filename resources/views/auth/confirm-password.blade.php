<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center sm:text-left">
            <p class="admin-kicker">Confirmation</p>
            <h1 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Zone securisee</h1>
            <p class="admin-copy mt-3">
                Confirmez votre mot de passe avant d'accéder à cette action sensible.
            </p>
        </div>

        <div class="admin-empty-state">
            Cette vérification protège les modifications importantes de l'administration.
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="password" value="Mot de passe" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end pt-2">
                <x-primary-button>
                    Confirmer
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
