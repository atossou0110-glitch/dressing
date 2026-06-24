@php
    $features = collect($product['features'] ?? [])->take(2)->values();
    $productViews = collect($product['galleryUrls'] ?? [
            $product['imageUrl'] ?? null,
            $product['secondaryImageUrl'] ?? null,
        ])
        ->filter()
        ->unique()
        ->values();

    if ($productViews->isEmpty()) {
        $productViews = collect(['/images/commode face .png']);
    }
@endphp

<article class="brand-card brand-product-card group flex h-full flex-col overflow-hidden" data-reveal>
    <a
        href="{{ $product['detailsUrl'] }}"
        class="brand-card-media-frame brand-product-viewer relative aspect-[6/5] overflow-hidden bg-[var(--brand-ivory-2)]"
        data-product-viewer
        data-view-count="{{ $productViews->count() }}"
        style="text-decoration: none;"
    >
        <div class="brand-product-view-stack">
            @foreach ($productViews as $index => $imageUrl)
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $index === 0 ? $product['name'] : '' }}"
                    loading="lazy"
                    class="brand-product-view absolute inset-0 h-full w-full object-cover object-center {{ $index === 0 ? 'is-active' : '' }}"
                    data-product-view-index="{{ $index }}"
                    aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
                    style="--view-index: {{ $index }};"
                >
            @endforeach
        </div>

        @if ($productViews->count() > 1)
            <div class="brand-product-view-dots" aria-hidden="true">
                @foreach ($productViews as $index => $imageUrl)
                    <span class="brand-product-view-dot {{ $index === 0 ? 'is-active' : '' }}" data-product-view-dot="{{ $index }}"></span>
                @endforeach
            </div>
        @endif

        <!-- Nom du produit overlay -->
        <div class="absolute inset-x-0 top-0 flex items-center justify-center pt-4 px-3 z-10">
            <h4 class="brand-card-media-title text-center font-semibold text-sm leading-5">
                {{ $product['name'] }}
            </h4>
        </div>

        <div class="brand-card-media-meta absolute inset-x-4 bottom-4">
            <div>
                <p class="brand-card-media-label text-xs"><span aria-hidden="true">&#x1F3F7;&#xFE0F;</span> Prix</p>
                <p class="brand-card-media-price text-sm">{{ $product['homePrice'] }}</p>
            </div>
        </div>
    </a>
</article>
