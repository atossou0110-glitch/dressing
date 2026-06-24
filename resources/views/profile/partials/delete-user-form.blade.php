<section class="space-y-6">
    <header>
        <p class="admin-kicker">Compte</p>
        <h2 class="mt-3 text-2xl font-semibold text-[var(--brand-ink)]">Supprimer le compte</h2>
        <p class="admin-copy mt-3">
            Cette action supprimera definitivement votre compte et ses donnees.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center justify-center rounded-[999px] border border-[rgba(176,73,68,0.22)] bg-[rgba(255,241,239,0.8)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-[#8b3733] transition hover:bg-[rgba(255,232,230,0.95)]"
    >
        Supprimer le compte
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5 p-6">
            @csrf
            @method('delete')

            <div>
                <p class="admin-kicker">Confirmation</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">
                    Voulez-vous vraiment supprimer ce compte ?
                </h2>
                <p class="mt-3 text-sm leading-7 text-white/70">
                    Cette action est irreversible. Entrez votre mot de passe pour confirmer la suppression definitive.
                </p>
            </div>

            <div>
                <label for="password" class="sr-only">Mot de passe</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="admin-input w-full"
                    placeholder="Mot de passe"
                >
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Annuler
                </button>

                <button type="submit" class="inline-flex items-center justify-center rounded-[999px] border border-[rgba(176,73,68,0.22)] bg-[rgba(255,241,239,0.8)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-[#8b3733] transition hover:bg-[rgba(255,232,230,0.95)]">
                    Supprimer
                </button>
            </div>
        </form>
    </x-modal>
</section>
