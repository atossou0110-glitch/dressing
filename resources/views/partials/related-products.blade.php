@props(['products' => []])

@php
    // Filter to show only 4 related products
    $displayProducts = array_slice($products, 0, 4);
@endphp

@if (count($displayProducts) > 0)
    <section class="brand-section-light py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h2 class="brand-display text-3xl text-[var(--brand-ink)] sm:text-4xl">Vous aimerez aussi</h2>
                <p class="mt-3 text-base text-[var(--brand-copy)]">Complétez votre sélection avec ces produits similaires</p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($displayProducts as $product)
                    <div class="group cursor-pointer" data-reveal>
                        <!-- Product Image -->
                        <div class="relative overflow-hidden rounded-lg bg-[var(--brand-panel)] mb-4 aspect-[3/4]">
                            <img 
                                src="{{ $product['imageUrl'] ?? asset('images/commod.png') }}"
                                alt="{{ $product['name'] ?? 'Produit' }}"
                                class="w-full h-full object-cover transition duration-300 group-hover:scale-110"
                            >
                            
                            <!-- Hover overlay with button -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition duration-300 flex items-center justify-center">
                                <a 
                                    href="{{ $product['detailsUrl'] ?? '#' }}"
                                    class="opacity-0 group-hover:opacity-100 transition duration-300 brand-button py-3 px-6 text-sm font-semibold"
                                >
                                    Voir produit
                                </a>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div>
                            <!-- Category -->
                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--brand-sand)]">
                                {{ $product['categoryLabel'] ?? 'Produit' }}
                            </p>

                            <!-- Name -->
                            <h3 class="mt-2 text-sm font-semibold text-[var(--brand-ink)] line-clamp-2">
                                {{ $product['name'] ?? 'Produit sans nom' }}
                            </h3>

                            <!-- Rating -->
                            @if (($product['reviewCount'] ?? 0) > 0)
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="text-xs">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($product['averageRating'] ?? 0))
                                                <span class="text-[var(--brand-sand)]">★</span>
                                            @else
                                                <span class="text-gray-300">★</span>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-xs text-[var(--brand-copy)]">
                                        ({{ $product['reviewCount'] ?? 0 }})
                                    </p>
                                </div>
                            @endif

                            <!-- Price -->
                            <p class="mt-3 text-lg font-bold text-[var(--brand-deep)]">
                                {{ $product['homePrice'] ?? 'Prix sur demande' }}
                            </p>

                            <!-- CTA Buttons -->
                            <div class="mt-4 flex gap-2">
                                <a 
                                    href="{{ $product['detailsUrl'] ?? '#' }}"
                                    class="flex-1 brand-button-secondary py-2 px-3 text-xs font-semibold text-center transition"
                                >
                                    Détails
                                </a>
                                <a 
                                    href="{{ $product['checkoutUrl'] ?? '#' }}"
                                    class="flex-1 brand-button py-2 px-3 text-xs font-semibold text-center transition"
                                >
                                    Panier
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
