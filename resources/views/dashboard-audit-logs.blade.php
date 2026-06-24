<x-app-layout>
    <x-slot:header>
        <div class="admin-page-header">
            <div>
                <p class="admin-kicker">Historique</p>
                <h1 class="admin-title mt-3">Audit Trail - Modifications</h1>
                <p class="admin-copy mt-3 max-w-4xl">
                    Suivi complet de toutes les modifications effectuées par les administrateurs sur le site.
                </p>
            </div>

            <a href="{{ route('admin.export.audit-logs') }}" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                ⬇️ Exporter en CSV
            </a>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        <!-- Filtres -->
        <section class="admin-surface px-6 py-6 sm:px-8">
            <form method="get" class="space-y-4" x-data="{ open: false }">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[250px]">
                        <label class="block text-sm font-medium text-[var(--brand-ink)] mb-2">Recherche</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Description, action, admin..."
                            class="w-full px-4 py-2.5 border border-[var(--brand-line)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand-accent)]"
                        >
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-[var(--brand-ink)] mb-2">Action</label>
                        <select name="action" class="w-full px-4 py-2.5 border border-[var(--brand-line)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand-accent)]">
                            <option value="">-- Tous --</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act }}" @selected(request('action') === $act)>
                                    {{ ucfirst($act) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-[var(--brand-ink)] mb-2">Modèle</label>
                        <select name="model" class="w-full px-4 py-2.5 border border-[var(--brand-line)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--brand-accent)]">
                            <option value="">-- Tous --</option>
                            @foreach ($modelTypes as $modelType)
                                <option value="{{ $modelType }}" @selected(request('model') === $modelType)>
                                    {{ $modelType }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="brand-button-primary px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.14em]">
                            Filtrer
                        </button>
                        @if (request()->filled('search') || request()->filled('action') || request()->filled('model'))
                            <a href="{{ route('dashboard.audit-logs') }}" class="brand-button-secondary px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.14em]">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </section>

        <!-- Tableau Audit Logs -->
        <section class="admin-surface px-6 py-6 sm:px-8 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[var(--brand-line)]">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-[var(--brand-ink)]">Date/Heure</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-[var(--brand-ink)]">Admin</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-[var(--brand-ink)]">Action</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-[var(--brand-ink)]">Modèle</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-[var(--brand-ink)]">Description</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-[var(--brand-ink)]">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="border-b border-[var(--brand-line)] hover:bg-[var(--brand-panel)] transition">
                            <td class="py-3 px-4 text-sm">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="py-3 px-4 text-sm font-medium">{{ $log->user?->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-sm">
                                <span class="admin-pill">{{ strtoupper($log->action) }}</span>
                            </td>
                            <td class="py-3 px-4 text-sm">{{ $log->model_type }}</td>
                            <td class="py-3 px-4 text-sm">{{ $log->description ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Aucun log trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($logs->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
