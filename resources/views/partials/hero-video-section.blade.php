@props([
    'product' => null,
    'copy' => [],
])

@php
    if (! $product && isset($featuredProduct)) {
        $product = $featuredProduct;
    }

    if (! $product) {
        $product = [
            'name' => 'Dressing Ivoire Modulable',
            'code' => 'E',
            'collection' => 'dressing',
            'homePrice' => '899 000 FCFA',
            'homeDescription' => 'Une sélection premium pour les grands volumes de rangements',
            'imageUrl' => asset('images/commod.png'),
            'secondaryImageUrl' => asset('images/commod.png'),
        ];
    }

    $productName = is_array($product) ? ($product['name'] ?? 'Produit Premium') : ($product->name ?? 'Produit Premium');
    $productCode = is_array($product) ? ($product['code'] ?? 'N/A') : ($product->code ?? 'N/A');
    $productPrice = is_array($product) ? ($product['homePrice'] ?? ($product['price'] ?? 'Prix sur demande')) : ($product->homePrice ?? ($product->price ?? 'Prix sur demande'));
    $productDescription = is_array($product) ? ($product['homeDescription'] ?? ($product['description'] ?? '')) : ($product->homeDescription ?? ($product->description ?? ''));
    $productImage = is_array($product) ? ($product['imageUrl'] ?? asset('images/commod.png')) : ($product->imageUrl ?? asset('images/commod.png'));
    $productSecondaryImage = is_array($product) ? ($product['secondaryImageUrl'] ?? $productImage) : ($product->secondaryImageUrl ?? $productImage);
    $productCollection = is_array($product)
        ? ($product['collection'] ?? 'dressing')
        : (method_exists($product, 'storefrontCollection') ? $product->storefrontCollection() : 'dressing');

    $sectionEyebrow = array_key_exists('eyebrow', $copy) ? $copy['eyebrow'] : 'À la une';
    $sectionTitle = array_key_exists('title', $copy) ? $copy['title'] : $productName;
    $sectionDescription = array_key_exists('description', $copy)
        ? $copy['description']
        : ($productDescription ?: 'Un meuble conçu avec soin pour transformer votre espace de vie en véritable refuge avec plus de rangements et de style.');
    $sectionChips = array_key_exists('chips', $copy) ? $copy['chips'] : ['3 capsules vidéo', 'lecture silencieuse'];
    $sectionMetaLabel = array_key_exists('meta_label', $copy) ? $copy['meta_label'] : 'Code produit';
    $sectionMetaValue = array_key_exists('meta_value', $copy) ? $copy['meta_value'] : $productCode;
    $sectionPrice = array_key_exists('price', $copy) ? $copy['price'] : $productPrice;
    $primaryCtaLabel = array_key_exists('primary_cta_label', $copy) ? $copy['primary_cta_label'] : 'Voir la collection';
    $secondaryCtaLabel = array_key_exists('secondary_cta_label', $copy) ? $copy['secondary_cta_label'] : 'Decouvrir les solutions King';
    $clipLabels = array_key_exists('clip_labels', $copy) ? $copy['clip_labels'] : ['Visite immersive', 'Ambiance intérieure', 'Projection dressing'];
    $showMeta = filled($sectionMetaLabel) && filled($sectionMetaValue);
    $showPrice = filled($sectionPrice);
    $showPrimaryCta = filled($primaryCtaLabel);
    $showSecondaryCta = filled($secondaryCtaLabel);

    $videoPlaylist = [
        [
            'src' => asset('videos/showcase-main.mp4'),
            'label' => $clipLabels[0] ?? 'Visite immersive',
            'eyebrow' => 'Séquence 01',
            'poster' => $productImage,
        ],
        [
            'src' => asset('videos/showcase-interior.mp4'),
            'label' => $clipLabels[1] ?? 'Ambiance intérieure',
            'eyebrow' => 'Séquence 02',
            'poster' => $productSecondaryImage,
        ],
        [
            'src' => asset('videos/showcase-wardrobe.mp4'),
            'label' => $clipLabels[2] ?? 'Projection dressing',
            'eyebrow' => 'Séquence 03',
            'poster' => $productImage,
        ],
    ];

    $primaryVideo = $videoPlaylist[0];
    $secondaryVideos = array_slice($videoPlaylist, 1);
@endphp

<section class="brand-hero-video relative overflow-hidden py-12 lg:py-20">
    <div class="absolute inset-0">
        <div class="relative h-full w-full bg-black/5">
            <video
                class="brand-hero-video-main h-full w-full object-cover"
                autoplay
                muted
                loop
                playsinline
                preload="auto"
                data-smart-video
            >
                <source src="{{ $primaryVideo['src'] }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-gradient-to-r from-[rgba(3,20,24,0.78)] via-[rgba(3,20,24,0.48)] to-[rgba(3,20,24,0.22)]"></div>
        </div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="storefront-fixed-video-grid grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-end xl:grid-cols-[minmax(0,1fr)_26rem]">
            <div class="max-w-2xl">
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                        <p class="brand-kicker text-[var(--brand-sand)]">{{ $sectionEyebrow }}</p>
                        <h2 class="brand-display text-3xl font-bold leading-tight text-[var(--brand-sand)] sm:text-4xl lg:text-5xl">
                            {{ $sectionTitle }}
                        </h2>
                        <p class="max-w-lg text-base leading-7 text-[var(--brand-sand-soft)]">
                            {{ $sectionDescription }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @foreach ($sectionChips as $chip)
                            <span class="brand-hero-video-chip">
                                @if ($loop->first)
                                    <span aria-hidden="true">&#x1F3AC;</span>
                                @else
                                    <span aria-hidden="true">&#x1F50A;</span>
                                @endif
                                {{ $chip }}
                            </span>
                        @endforeach
                    </div>

                    @if ($showMeta || $showPrice || $showPrimaryCta || $showSecondaryCta)
                        <div class="flex flex-col gap-4 pt-4">
                            @if ($showMeta || $showPrice)
                                <div class="space-y-2">
                                    @if ($showMeta)
                                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--brand-sand)]">
                                            <span aria-hidden="true">&#x1F3F7;&#xFE0F;</span> {{ $sectionMetaLabel }}: <span class="text-lg font-bold">{{ $sectionMetaValue }}</span>
                                        </p>
                                    @endif
                                    @if ($showPrice)
                                        <p class="text-3xl font-bold text-white">
                                            {{ $sectionPrice }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if ($showPrimaryCta || $showSecondaryCta)
                                <div @class(['storefront-fixed-row storefront-fixed-wrap flex flex-col gap-3 sm:flex-row', 'pt-4' => $showMeta || $showPrice])>
                                    @if ($showPrimaryCta)
                                        <a href="{{ route('catalog.index', ['collection' => $productCollection]) }}" class="brand-button inline-flex w-fit items-center justify-center gap-2 px-6 py-3 text-sm font-semibold uppercase tracking-[0.14em] transition hover:shadow-lg">
                                            {{ $primaryCtaLabel }}
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    @endif
                                    @if ($showSecondaryCta)
                                        <a href="{{ route('catalog.dr-dressing') }}" class="brand-button-secondary inline-flex w-fit items-center justify-center gap-2 px-6 py-3 text-sm font-semibold uppercase tracking-[0.14em] transition">
                                            {{ $secondaryCtaLabel }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid gap-4 lg:self-end">
                @foreach ($secondaryVideos as $index => $clip)
                    <article class="brand-hero-video-aside-card p-3" data-reveal="right" data-reveal-delay="{{ 140 + ($index * 90) }}ms">
                        <div class="brand-hero-video-aside-media">
                            <video
                                class="h-full w-full object-cover"
                                autoplay
                                muted
                                loop
                                playsinline
                                preload="auto"
                                data-smart-video
                            >
                                <source src="{{ $clip['src'] }}" type="video/mp4">
                            </video>
                        </div>
                        <div class="mt-3">
                            <p class="text-[0.62rem] font-semibold uppercase tracking-[0.16em] text-[rgba(255,239,214,0.7)]">
                                {{ $clip['eyebrow'] }}
                            </p>
                            <p class="mt-1 text-sm font-semibold text-[var(--brand-sand)]">
                                {{ $clip['label'] }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
        <div class="absolute top-0 right-0 h-72 w-72 rounded-full bg-[var(--brand-sand)] blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-[var(--brand-deep)] blur-3xl"></div>
    </div>
</section>

