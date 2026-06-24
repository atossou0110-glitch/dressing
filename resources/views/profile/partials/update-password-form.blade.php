<section class="space-y-6">
    <header>
        <p class="admin-kicker">Sécurité</p>
        <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Mot de passe</h2>
        <p class="admin-copy mt-3">
            Utilisez un mot de passe long et unique pour protéger l'administration.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="admin-field-label">Mot de passe actuel</label>
            <input id="update_password_current_password" name="current_password" type="password" class="admin-input" autocomplete="current-password">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="admin-field-label">Nouveau mot de passe</label>
            <input id="update_password_password" name="password" type="password" class="admin-input" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="admin-field-label">Confirmer le mot de passe</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="admin-input" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                Enregistrer
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="admin-helper"
                >Enregistré.</p>
            @endif
        </div>
    </form>
</section>
