<x-app-layout>
    <x-slot:header>
        <div class="admin-page-header">
            <div>
                <p class="admin-kicker">Administration</p>
                <h1 class="admin-title mt-3">Profil</h1>
                <p class="admin-copy mt-3 max-w-3xl">
                    Mettez à jour vos informations personnelles et les paramètres de sécurité dans un espace aligné sur le reste du dashboard.
                </p>
            </div>
        </div>
    </x-slot:header>

    <div class="space-y-8">
        <section class="admin-surface px-6 py-6 sm:px-8">
            @include('profile.partials.update-profile-information-form')
        </section>

        <section class="admin-surface px-6 py-6 sm:px-8">
            @include('profile.partials.update-password-form')
        </section>

        <section class="admin-surface px-6 py-6 sm:px-8">
            @include('profile.partials.delete-user-form')
        </section>
    </div>
</x-app-layout>
