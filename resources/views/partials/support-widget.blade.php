@php
    $supportProductSlug = $supportProductSlug ?? null;
@endphp

<div
    data-support-widget
    data-chat-url="{{ route('support.chat') }}"
    data-source-path="/{{ trim(request()->path(), '/') }}"
    @if ($supportProductSlug)
        data-source-product-slug="{{ $supportProductSlug }}"
    @endif
    class="fixed bottom-5 right-5 z-40"
>
    <button
        type="button"
        data-support-toggle
        class="brand-button-primary brand-support-launcher inline-flex items-center gap-3 rounded-full px-5 py-4 text-left shadow-[0_18px_40px_rgba(2,25,31,0.22)]"
    >
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/12 text-xl" aria-hidden="true">&#x1F4AC;</span>
        <span class="flex flex-col">
            <span class="text-sm font-semibold uppercase tracking-[0.12em]">Assistant boutique</span>
            <span class="text-xs text-[var(--brand-sand-soft)]">Réponses automatiques instantanées</span>
        </span>
    </button>

    <div data-support-panel class="brand-support-panel mt-3 hidden h-[min(35rem,calc(100vh-2rem))] w-[min(26rem,calc(100vw-2rem))] flex flex-col overflow-hidden rounded-[1.75rem] border border-[rgba(211,176,130,0.18)] bg-white shadow-[0_24px_70px_rgba(2,25,31,0.24)]">
        <div class="shrink-0 bg-[linear-gradient(135deg,rgba(8,62,73,1),rgba(17,122,139,0.92))] px-5 py-4 text-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand-sand-soft)]">Support</p>
                    <h3 class="mt-2 text-xl font-semibold">Conseil King Rangement</h3>
                    <p class="mt-2 text-sm leading-6 text-[rgba(244,236,226,0.88)]">
                        Posez une question sur le produit, la livraison, le paiement ou les reductions flash.
                    </p>
                </div>

                <button type="button" data-support-close class="brand-modal-close inline-flex h-10 w-10 items-center justify-center border-white/20 bg-white/10 text-lg text-white/90 transition hover:border-white/40 hover:text-white">
                    x
                </button>
            </div>
        </div>

        <div data-support-messages class="brand-support-messages min-h-0 flex-1 space-y-3 overflow-y-auto px-4 py-4">
            <article class="max-w-[85%] rounded-[1.4rem] rounded-tl-md bg-white px-4 py-3 text-sm leading-7 text-[var(--brand-copy)] shadow-[0_12px_26px_rgba(2,25,31,0.08)]">
                Bonjour. Je peux vous guider sur le choix d un meuble, les paiements, la livraison et les offres flash en cours.
            </article>
        </div>

        <div class="shrink-0 border-t border-[var(--brand-line)] bg-white px-4 py-4">
            <div data-support-quick-actions class="mb-3 flex max-h-28 flex-wrap gap-2 overflow-y-auto pr-1">
                @foreach ([
                    'Quel est le delai de livraison ?',
                    'Comment payer mon produit ?',
                    'Je veux parler a un expert',
                ] as $quickMessage)
                    <button
                        type="button"
                        data-support-quick="{{ $quickMessage }}"
                        class="rounded-full border border-[rgba(8,62,73,0.14)] bg-[rgba(255,247,239,0.72)] px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-[var(--brand-ink)] transition hover:border-[var(--brand-deep)] hover:bg-[rgba(255,241,233,0.96)]"
                    >
                        {{ $quickMessage }}
                    </button>
                @endforeach
            </div>

            <div data-support-feedback class="mb-3 hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

            <form data-support-form class="flex items-end gap-3">
                <div class="min-w-0 flex-1">
                    <label for="support-message" class="sr-only">Votre message</label>
                    <textarea
                        id="support-message"
                        data-support-input
                        rows="2"
                        class="brand-input min-h-[3.25rem] w-full resize-none px-4 py-3 text-sm"
                        placeholder="Expliquez votre besoin..."
                    ></textarea>
                </div>

                <button type="submit" class="brand-button-primary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Envoyer
                </button>
            </form>
        </div>
    </div>
</div>
