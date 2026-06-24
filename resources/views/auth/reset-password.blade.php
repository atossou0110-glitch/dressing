<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center sm:text-left">
            <p class="admin-kicker">Sécurité</p>
            <h1 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Réinitialiser le mot de passe</h1>
            <p class="admin-copy mt-3">
                Définissez un nouveau mot de passe pour retrouver l'accès à votre compte.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" value="Nouveau mot de passe" />
                <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex justify-end pt-2">
                <x-primary-button>
                    Réinitialiser
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
