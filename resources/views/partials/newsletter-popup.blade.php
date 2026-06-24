@php
    $newsletterSourceProductSlug = $newsletterSourceProductSlug ?? null;
@endphp

<div data-newsletter-modal class="brand-newsletter-backdrop fixed inset-0 z-50 hidden items-center justify-center bg-[rgba(2,25,31,0.62)] px-4 py-6">
    <div class="brand-newsletter-panel relative w-full max-w-lg rounded-[2rem] border border-[rgba(211,176,130,0.18)] p-6 shadow-[0_30px_90px_rgba(2,25,31,0.26)] sm:p-8">
        <button
            type="button"
            data-newsletter-close
            class="brand-modal-close absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center text-lg"
            aria-label="Fermer la newsletter"
        >
            x
        </button>

        <p class="brand-kicker brand-kicker-light"><span aria-hidden="true">&#x2709;&#xFE0F;</span> Newsletter</p>
        <h2 class="mt-3 text-3xl font-semibold text-[var(--brand-ink)]">Recevez nos réductions privées</h2>
        <p class="mt-4 text-sm leading-7 text-[var(--brand-copy)]">
            Abonnez-vous pour suivre les nouvelles pieces, les offres flash et les conseils d'optimisation de King Rangement Benin.
        </p>

        <form data-newsletter-form action="{{ route('newsletter.subscribe') }}" class="mt-8 space-y-4">
            <div data-newsletter-feedback class="hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

            <div>
                <label for="newsletter-email" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                    Votre email
                </label>
                <input
                    id="newsletter-email"
                    type="email"
                    name="email"
                    required
                    class="brand-input w-full px-4 py-3 text-sm"
                    placeholder="vous@exemple.com"
                >
            </div>

            <div>
                <label for="newsletter-name" class="mb-2 block text-sm font-semibold uppercase tracking-[0.14em] text-[var(--brand-teal-soft)]">
                    Prenom
                </label>
                <input
                    id="newsletter-name"
                    type="text"
                    name="name"
                    class="brand-input w-full px-4 py-3 text-sm"
                    placeholder="Votre prenom"
                >
            </div>

            <input type="hidden" name="source_path" value="/{{ trim(request()->path(), '/') }}">
            @if ($newsletterSourceProductSlug)
                <input type="hidden" name="source_product_slug" value="{{ $newsletterSourceProductSlug }}">
            @endif

            <label class="flex items-start gap-3 rounded-2xl border border-[var(--brand-line)] bg-[rgba(255,247,239,0.68)] px-4 py-3 text-sm leading-6 text-[var(--brand-copy)]">
                <input type="checkbox" name="agree" value="1" required class="mt-1 rounded border-[var(--brand-line)] text-[var(--brand-deep)] focus:ring-[var(--brand-deep)]">
                <span>J accepte de recevoir les offres, les reductions flash et les conseils de la marque.</span>
            </label>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="brand-button-primary inline-flex flex-1 items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Obtenir mon code
                </button>
                <button type="button" data-newsletter-close class="brand-button-secondary inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold uppercase tracking-[0.14em]">
                    Plus tard
                </button>
            </div>
        </form>
    </div>
</div>

