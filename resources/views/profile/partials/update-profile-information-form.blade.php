@php
    $profileUser = $user ?? auth()->user();
@endphp

<section class="space-y-6">
    <header>
        <p class="admin-kicker">Compte</p>
        <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Informations du profil</h2>
        <p class="admin-copy mt-3">
            Mettez à jour votre nom et votre adresse email.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="admin-field-label">Nom</label>
            <input id="name" name="name" type="text" class="admin-input" value="{{ old('name', $profileUser?->name) }}" required autofocus autocomplete="name">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="admin-field-label">Email</label>
            <input id="email" name="email" type="email" class="admin-input" value="{{ old('email', $profileUser?->email) }}" required autocomplete="username">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($profileUser instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $profileUser->hasVerifiedEmail())
                <div class="mt-3 space-y-2">
                    <p class="admin-copy">
                        Votre adresse email n'est pas encore vérifiée.
                    </p>

                    <button form="send-verification" class="text-sm font-semibold uppercase tracking-[0.12em] text-[var(--brand-sand-dark)] transition hover:text-[var(--brand-ink)]">
                        Renvoyer l'email de vérification
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="admin-helper">
                            Un nouveau lien de vérification a été envoyé à votre adresse email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                Enregistrer
            </button>

            @if (session('status') === 'profile-updated')
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
