<?php

namespace App\Http\Controllers;

use App\Models\FlashCampaign;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SiteSetting;
use App\Support\LoyaltyProgramService;
use App\Support\ProductGalleryManager;
use App\Support\ProductInteractionTracker;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CatalogController extends Controller
{
    /**
     * Display labels used in the storefront.
     *
     * @var array<string, string>
     */
    private const CATEGORY_LABELS = [
        'commode' => 'Commode',
        'etagere' => 'Etagere',
        'dressing' => 'Dressing',
        'armoire' => 'Armoire',

    ];

    /**
     * Storefront collection labels.
     *
     * @var array<string, string>
     */
    private const COLLECTION_LABELS = [
        'rangement' => 'Rangement',
        'dressing' => 'Dressing',
    ];

    /**
     * Budget labels used by the catalog filters.
     *
     * @var array<string, string>
     */
    private const BUDGET_LABELS = [
        'compact' => 'Jusqu a 300 000 FCFA',
        'equilibre' => '301 000 a 700 000 FCFA',
        'signature' => 'Plus de 700 000 FCFA',
    ];

    public function __construct(
        private readonly ProductGalleryManager $galleryManager,
        private readonly ProductInteractionTracker $interactionTracker,
        private readonly LoyaltyProgramService $loyaltyProgram,
    ) {}

    /**
     * Display the catalog landing page.
     */
    public function index(Request $request): View
    {
        Product::syncRequiredCatalog();

        $products = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('code')
            ->get();

        $visibleProducts = $this->visibleProducts($products);
        $filteredProducts = $this->applyCatalogFilters($visibleProducts, $request);
        $preferredProducts = $filteredProducts->isNotEmpty() ? $filteredProducts : $visibleProducts;

        return view('dressingue', [
            'heroProducts' => $preferredProducts
                ->take(4)
                ->map(fn (Product $product) => $this->catalogCardData($product, $request))
                ->all(),
            'featuredProducts' => $preferredProducts
                ->take(4)
                ->map(fn (Product $product) => $this->catalogCardData($product, $request))
                ->all(),
            'rangementProducts' => $this->collectionCards($filteredProducts, 'rangement', $request),
            'dressingProducts' => $this->collectionCards($filteredProducts, 'dressing', $request),
            'searchResults' => $request->filled('q')
                ? $filteredProducts
                    ->map(fn (Product $product) => $this->catalogCardData($product, $request))
                    ->all()
                : [],
            'catalogFilters' => $this->filterViewData($request, $visibleProducts, $filteredProducts),
            'buyingGuides' => $this->buyingGuides($visibleProducts, $request),
            'recentReviews' => ProductReview::query()
                ->with('product:id,slug,code,name,category')
                ->latest()
                ->get()
                ->filter(fn (ProductReview $review) => $review->product !== null && $this->isVisibleCategory((string) $review->product->category))
                ->take(6)
                ->values(),
            'loyaltySummary' => $this->loyaltyProgram->snapshotForUser($request->user()),
            'activeFlashCampaign' => FlashCampaign::query()->activeNow()->latest('starts_at')->latest('id')->first(),
            'whatsAppSupportUrl' => $this->whatsAppSupportUrl(),
        ]);
    }

    /**
     * Display the dedicated King solutions page.
     */
    public function drDressing(Request $request): View
    {
        Product::syncRequiredCatalog();

        $products = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('code')
            ->get()
            ->filter(fn (Product $product) => $product->storefrontCollection() === 'dressing')
            ->values();

        $visibleProducts = $this->visibleProducts($products);
        $filteredProducts = $this->applyCatalogFilters($visibleProducts, $request, 'dressing', ['dressing', 'armoire']);
        $preferredProducts = $filteredProducts->isNotEmpty() ? $filteredProducts : $visibleProducts;

        $miniCategories = collect(['dressing', 'armoire'])
            ->map(function (string $category) use ($filteredProducts, $request): array {
                return [
                    'slug' => $category,
                    'label' => $this->labelForCategory($category),
                    'description' => match ($category) {
                        'dressing' => 'Compositions ouvertes et modulables pour structurer une chambre parentale comme un vrai espace dressing.',
                        'armoire' => 'Volumes fermes et rassurants pour les chambres, suites et projets d amenagement complets.',
                        default => 'Selection de produits.',
                    },
                    'products' => $filteredProducts
                        ->filter(fn (Product $product) => $product->category === $category)
                        ->values()
                        ->map(fn (Product $product) => $this->catalogCardData($product, $request))
                        ->all(),
                ];
            })
            ->filter(fn (array $section) => count($section['products']) > 0)
            ->values()
            ->all();

        return view('dr-dressing', [
            'heroProducts' => $preferredProducts
                ->take(3)
                ->map(fn (Product $product) => $this->catalogCardData($product, $request))
                ->all(),
            'searchResults' => $request->filled('q')
                ? $filteredProducts
                    ->map(fn (Product $product) => $this->catalogCardData($product, $request))
                    ->all()
                : [],
            'miniCategories' => $miniCategories,
            'catalogFilters' => $this->filterViewData($request, $visibleProducts, $filteredProducts, 'dressing', ['dressing', 'armoire']),
            'buyingGuides' => $this->buyingGuides($visibleProducts, $request, true),
            'loyaltySummary' => $this->loyaltyProgram->snapshotForUser($request->user()),
            'activeFlashCampaign' => FlashCampaign::query()->activeNow()->latest('starts_at')->latest('id')->first(),
            'serviceHighlights' => [
                ['icon' => '01', 'title' => 'Plus d images', 'description' => '3 vues minimum par produit pour mieux visualiser volumes et designs.'],
                ['icon' => '02', 'title' => 'Descriptions detaillees', 'description' => 'Specifications completes, dimensions et mode d utilisation.'],
                ['icon' => '03', 'title' => 'Avis visibles', 'description' => 'Notes et commentaires pour rassurer avant la commande.'],
                ['icon' => '04', 'title' => 'Logistique claire', 'description' => 'Delais, livraison et conditions expliques simplement.'],
            ],
        ]);
    }

    /**
     * Serve a managed product gallery image directly from the local source folder.
     */
    public function productImage(Product $product, string $filename): BinaryFileResponse
    {
        $path = $this->galleryManager->imagePath($product, $filename);

        abort_unless($path !== null, 404);

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Keep backward compatibility for older commode image URLs.
     */
    public function legacyCommodeImage(string $filename): BinaryFileResponse
    {
        $product = Product::resolveCatalogProduct('produit-a');

        abort_unless($product instanceof Product, 404);

        return $this->productImage($product, $filename);
    }

    /**
     * Serve images from COMMODE ET ETAGERE folder.
     */
    public function commodEtagereImage(string $filename): BinaryFileResponse
    {
        $path = base_path('COMMODE ET ETAGERE/' . $filename);

        abort_unless(file_exists($path), 404);

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Serve images from DRESSING folder.
     */
    public function dressingImage(string $filename): BinaryFileResponse
    {
        $path = base_path('DRESSING/' . $filename);

        abort_unless(file_exists($path), 404);

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Display the detail page for product A.
     */
    public function showProductA(Request $request): View
    {
        return $this->showLegacyProduct($request, 'produit-a');
    }

    /**
     * Display the detail page for product B.
     */
    public function showProductB(Request $request): View
    {
        return $this->showLegacyProduct($request, 'produit-b');
    }

    /**
     * Display the detail page for product C.
     */
    public function showProductC(Request $request): View
    {
        return $this->showLegacyProduct($request, 'produit-c');
    }

    /**
     * Display a detail page by product slug.
     */
    public function show(Request $request, Product $product): View
    {
        Product::syncRequiredCatalog();

        $product = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($product->id);

        abort_unless($product->isStorefrontVisible(), 404);

        $product->load([
            'reviews' => fn ($query) => $query->latest()->limit(12),
        ]);

        return view('product-detail', [
            'product' => $this->detailData($product, $request),
            'productModel' => $product,
            'productGallery' => $this->productGallery($product),
            'reviews' => $product->reviews,
            'suggestedProducts' => $this->suggestedCards($product, $request),
            'detailAdvice' => $this->detailAdvice($product),
            'collectionLabel' => $this->labelForCollection($product->storefrontCollection()),
            'categoryLabel' => $this->labelForCategory((string) $product->category),
            'loyaltySummary' => $this->loyaltyProgram->snapshotForUser($request->user()),
            'activeFlashCampaign' => FlashCampaign::query()->activeNow()->latest('starts_at')->latest('id')->first(),
        ]);
    }

    /**
     * Resolve cards for a collection row.
     *
     * @return list<array<string, mixed>>
     */
    private function collectionCards(Collection $products, string $collection, Request $request): array
    {
        return $products
            ->filter(fn (Product $product) => $product->storefrontCollection() === $collection)
            ->values()
            ->map(fn (Product $product) => $this->catalogCardData($product, $request))
            ->all();
    }

    /**
     * Display one of the legacy product URLs.
     */
    private function showLegacyProduct(Request $request, string $slug): View
    {
        $product = Product::resolveCatalogProduct($slug);

        abort_unless($product instanceof Product, 404);

        return $this->show($request, $product);
    }

    /**
     * Format product data for the homepage cards.
     *
     * @return array<string, mixed>
     */
    private function catalogCardData(Product $product, Request $request): array
    {
        $content = $product->resolvedContent();
        $gallery = $this->productGallery($product);

        return [
            'slug' => $product->slug,
            'code' => $product->code,
            'name' => $product->name,
            'category' => $product->category,
            'categoryLabel' => $this->labelForCategory((string) $product->category),
            'collection' => $product->storefrontCollection(),
            'collectionLabel' => $this->labelForCollection($product->storefrontCollection()),
            'homeBadge' => $content['home_badge'],
            'homeDescription' => $content['home_description'],
            'homePrice' => $content['home_price'],
            'homeHighlight' => $content['home_highlight'],
            'priceValue' => $this->priceValue($content['home_price'] ?? null),
            'features' => $content['features'],
            'specifications' => $content['specifications'],
            'dimensions' => $this->dimensionsFromSpecifications((array) ($content['specifications'] ?? [])),
            'reviewCount' => (int) ($product->reviews_count ?? 0),
            'averageRating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
            'detailsUrl' => route('products.show', $product),
            'checkoutUrl' => route('products.checkout.show', $product),
            'imageUrl' => $gallery[0] ?? '/images/commode face .png',
            'secondaryImageUrl' => $gallery[1] ?? ($gallery[0] ?? '/images/commode face .png'),
            'galleryUrls' => count($gallery) > 0 ? $gallery : ['/images/commode face .png'],
        ];
    }

    /**
     * Format product data for detail pages.
     *
     * @return array<string, mixed>
     */
    private function detailData(Product $product, Request $request): array
    {
        $content = $product->resolvedContent();
        $gallery = $this->productGallery($product);

        return [
            'slug' => $product->slug,
            'code' => $product->code,
            'name' => $product->name,
            'category' => $product->category,
            'categoryLabel' => $this->labelForCategory((string) $product->category),
            'collection' => $product->storefrontCollection(),
            'collectionLabel' => $this->labelForCollection($product->storefrontCollection()),
            'detailSubtitle' => $content['detail_subtitle'],
            'detailBadge' => $content['detail_badge'],
            'detailDescription' => $content['detail_description'],
            'homePrice' => $content['home_price'],
            'homeHighlight' => $content['home_highlight'],
            'features' => $content['features'],
            'specifications' => $content['specifications'],
            'preorderCount' => (int) $product->preorder_count,
            'reviewCount' => (int) ($product->reviews_count ?? 0),
            'averageRating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
            'hasPreordered' => $this->hasEffectivePreorder($request, $product),
            'detailsUrl' => route('products.show', $product),
            'checkoutUrl' => route('products.checkout.show', $product),
            'preorderUrl' => route('products.preorder', $product),
            'reviewUrl' => route('products.reviews.store', $product),
            'imageUrl' => $gallery[0] ?? '/images/commode face .png',
            'secondaryImageUrl' => $gallery[1] ?? ($gallery[0] ?? '/images/commode face .png'),
        ];
    }

    /**
     * Resolve product dimensions from the specification lines.
     *
     * @param  list<string>  $specifications
     * @return array{width: float, height: float, depth: float, label: string}|null
     */
    private function dimensionsFromSpecifications(array $specifications): ?array
    {
        foreach ($specifications as $specification) {
            $normalized = str_replace(',', '.', (string) $specification);

            if (! preg_match('/(\d+(?:\.\d+)?)\s*x\s*(\d+(?:\.\d+)?)\s*x\s*(\d+(?:\.\d+)?)/i', $normalized, $matches)) {
                continue;
            }

            $width = (float) $matches[1];
            $height = (float) $matches[2];
            $depth = (float) $matches[3];

            return [
                'width' => $width,
                'height' => $height,
                'depth' => $depth,
                'label' => sprintf(
                    '%s x %s x %s cm',
                    $this->formatDimension($width),
                    $this->formatDimension($height),
                    $this->formatDimension($depth),
                ),
            ];
        }

        return null;
    }

    /**
     * Normalize one storefront price string into an integer amount.
     */
    private function priceValue(?string $price): ?int
    {
        $digits = preg_replace('/\D+/', '', (string) $price);

        if ($digits === null || $digits === '') {
            return null;
        }

        return (int) $digits;
    }

    /**
     * Format one dimension value without trailing decimals when possible.
     */
    private function formatDimension(float $value): string
    {
        return fmod($value, 1.0) === 0.0
            ? (string) (int) $value
            : number_format($value, 1, '.', '');
    }

    /**
     * Resolve suggestion cards for a product detail page.
     *
     * @return list<array<string, mixed>>
     */
    private function suggestedCards(Product $product, Request $request): array
    {
        $sameCollection = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('id', '!=', $product->id)
            ->orderBy('code')
            ->get()
            ->filter(fn (Product $candidate) => $candidate->storefrontCollection() === $product->storefrontCollection())
            ->values();

        return $sameCollection
            ->filter(fn (Product $candidate) => $this->isVisibleCategory((string) $candidate->category))
            ->take(4)
            ->map(fn (Product $candidate) => $this->catalogCardData($candidate, $request))
            ->all();
    }

    /**
     * Remove hidden categories from the public storefront.
     */
    private function visibleProducts(Collection $products): Collection
    {
        return $products
            ->filter(fn (Product $product) => $product->isStorefrontVisible())
            ->values();
    }

    /**
     * Determine if a category should be visible on the storefront.
     */
    private function isVisibleCategory(string $category): bool
    {
        return in_array($category, Product::visibleStorefrontCategories(), true);
    }

    /**
     * Build the public WhatsApp support URL from the configured number.
     */
    private function whatsAppSupportUrl(): ?string
    {
        $number = preg_replace(
            '/\D+/',
            '',
            (string) SiteSetting::value('whatsapp_number', (string) config('services.whatsapp.number')),
        );

        if ($number === null || $number === '') {
            return null;
        }

        $message = "Bonjour, j'aimerais avoir des informations sur les solutions King Rangement Benin.";

        return sprintf('https://wa.me/%s?text=%s', $number, rawurlencode($message));
    }

    /**
     * Apply the public catalog search and filter set.
     */
    private function applyCatalogFilters(
        Collection $products,
        Request $request,
        ?string $forcedCollection = null,
        ?array $allowedCategories = null,
    ): Collection {
        $query = $this->normalizeCatalogSearchText((string) $request->query('q', ''));
        $selectedCategory = trim((string) $request->query('category', ''));
        $selectedCollection = $forcedCollection ?? trim((string) $request->query('collection', ''));
        $selectedBudget = trim((string) $request->query('budget', ''));
        $allowedCategories = $allowedCategories !== null ? array_values($allowedCategories) : null;

        return $products
            ->filter(function (Product $product) use ($allowedCategories, $query, $selectedBudget, $selectedCategory, $selectedCollection): bool {
                if ($allowedCategories !== null && ! in_array((string) $product->category, $allowedCategories, true)) {
                    return false;
                }

                if ($selectedCollection !== '' && $product->storefrontCollection() !== $selectedCollection) {
                    return false;
                }

                if ($selectedCategory !== '' && (string) $product->category !== $selectedCategory) {
                    return false;
                }

                if ($selectedBudget !== '' && ! $this->matchesBudget($product, $selectedBudget)) {
                    return false;
                }

                if ($query === '') {
                    return true;
                }

                return Str::contains($this->searchableProductText($product), $query);
            })
            ->values();
    }

    /**
     * Build the searchable text blob for a product.
     */
    private function searchableProductText(Product $product): string
    {
        $content = $product->resolvedContent();

        return $this->normalizeCatalogSearchText(implode(' ', array_filter([
            $product->name,
            $product->code,
            $this->labelForCategory((string) $product->category),
            $this->labelForCollection($product->storefrontCollection()),
            (string) ($content['home_description'] ?? ''),
            (string) ($content['detail_subtitle'] ?? ''),
            (string) ($content['detail_description'] ?? ''),
            implode(' ', array_map('strval', is_array($content['specifications'] ?? null) ? $content['specifications'] : [])),
        ])));
    }

    /**
     * Normalize free-text search so accents and punctuation do not block matches.
     */
    private function normalizeCatalogSearchText(string $value): string
    {
        return (string) Str::of(Str::ascii($value))
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/u', ' ')
            ->squish();
    }

    /**
     * Resolve filter options and current values for the views.
     *
     * @return array<string, mixed>
     */
    private function filterViewData(
        Request $request,
        Collection $sourceProducts,
        Collection $filteredProducts,
        ?string $forcedCollection = null,
        ?array $allowedCategories = null,
    ): array {
        $allowedCategories = $allowedCategories !== null ? array_values($allowedCategories) : null;

        $categoryOptions = $sourceProducts
            ->pluck('category')
            ->map(fn ($category) => (string) $category)
            ->filter(fn (string $category) => $allowedCategories === null || in_array($category, $allowedCategories, true))
            ->unique()
            ->values()
            ->map(fn (string $category) => [
                'value' => $category,
                'label' => $this->labelForCategory($category),
            ])
            ->all();

        $collectionOptions = $forcedCollection === null
            ? collect(self::COLLECTION_LABELS)
                ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
                ->values()
                ->all()
            : [];

        $budgetOptions = collect(self::BUDGET_LABELS)
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();

        $currentQuery = trim((string) $request->query('q', ''));
        $currentCategory = trim((string) $request->query('category', ''));
        $currentCollection = $forcedCollection ?? trim((string) $request->query('collection', ''));
        $currentBudget = trim((string) $request->query('budget', ''));

        return [
            'q' => $currentQuery,
            'category' => $currentCategory,
            'collection' => $currentCollection,
            'budget' => $currentBudget,
            'categoryOptions' => $categoryOptions,
            'collectionOptions' => $collectionOptions,
            'budgetOptions' => $budgetOptions,
            'resultsCount' => $filteredProducts->count(),
            'sourceCount' => $sourceProducts->count(),
            'hasActiveFilters' => $currentQuery !== '' || $currentCategory !== '' || ($forcedCollection === null && $currentCollection !== '') || $currentBudget !== '',
        ];
    }

    /**
     * Resolve a price band for a product.
     */
    private function matchesBudget(Product $product, string $budget): bool
    {
        $amount = $product->checkoutAmount();

        if ($amount === null) {
            return false;
        }

        return match ($budget) {
            'compact' => $amount <= 300000,
            'equilibre' => $amount > 300000 && $amount <= 700000,
            'signature' => $amount > 700000,
            default => true,
        };
    }

    /**
     * Build inspiration cards with deep-links to products.
     *
     * @return list<array<string, mixed>>
     */
    private function buyingGuides(Collection $products, Request $request, bool $forDrDressing = false): array
    {
        $links = $products
            ->mapWithKeys(fn (Product $product) => [$product->slug => $this->catalogCardData($product, $request)]);

        $guides = [
            [
                'eyebrow' => 'Guide d achat',
                'title' => 'Mesurez d abord la piece, puis choisissez le bon volume',
                'description' => 'Dans les sites de meubles qui vendent bien, le client se projette vite. Commencez toujours par la largeur disponible, la profondeur de passage et la hauteur sous plafond avant de choisir votre meuble.',
                'links' => $this->resolveGuideLinks($links, ['produit-a', 'produit-e', 'produit-h']),
            ],
            [
                'eyebrow' => 'Conseil rangement',
                'title' => 'Melangez modules fermes et zones ouvertes',
                'description' => 'Une facade totalement ouverte fatigue vite le regard, alors qu un mix portes, tiroirs et niches donne une impression plus premium et plus facile a vivre au quotidien.',
                'links' => $this->resolveGuideLinks($links, ['produit-f', 'produit-d', 'produit-c']),
            ],
            [
                'eyebrow' => 'Projection maison',
                'title' => 'Pensez circulation, miroir, linge et accessoires ensemble',
                'description' => 'Un bon site meuble ne montre pas seulement un produit, il montre l usage. Associez votre meuble principal a un coin miroir, un banc ou des paniers pour raconter une vraie scene de vie.',
                'links' => $this->resolveGuideLinks($links, ['produit-h', 'produit-a', 'produit-b']),
            ],
        ];

        if ($forDrDressing) {
            return array_slice($guides, 0, 2);
        }

        return $guides;
    }









    /**
     * Build extra product-page advice blocks.
     *
     * @return array<string, mixed>
     */
    private function detailAdvice(Product $product): array
    {
        $category = (string) $product->category;

        if ($category === 'etagere') {
            return [
                'heading' => 'Bien integrer cette etagere dans la piece',
                'description' => 'Sur les sites les plus convaincants, une etagere n est jamais presentee comme un simple volume vide: on explique ce qui restera visible, comment repartir le poids et quel decor elle soutient.',
                'cards' => [
                    [
                        'title' => 'Decidez ce qui restera visible',
                        'description' => 'Une etagere fonctionne mieux quand on choisit a l avance ce qui sera montre et ce qui sera cache dans des paniers ou des boites.',
                        'points' => [
                            'Garder les plus belles pieces a hauteur des yeux',
                            'Utiliser des boites pour les petits objets heterogenes',
                            'Eviter de saturer toutes les niches en meme temps',
                        ],
                    ],
                    [
                        'title' => 'Repartissez poids et hauteur',
                        'description' => 'Les objets lourds doivent stabiliser la base alors que les volumes decoratifs respirent mieux sur les niveaux superieurs.',
                        'points' => [
                            'Objets denses et paniers sur les tablettes basses',
                            'Livres et linge regroupes plutot que disperses',
                            'Pieces decoratives plus legeres en partie haute',
                        ],
                    ],
                    [
                        'title' => 'Ancrez la composition dans une vraie scene',
                        'description' => 'Une etagere parait tout de suite plus aboutie lorsqu elle dialogue avec un bureau, une tete de lit, une entree ou un angle lecture.',
                        'points' => [
                            'Laisser quelques zones volontairement aeriennes',
                            'Associer un miroir, une lampe ou un tapis simple',
                            'Verifier la fixation selon le mur et la charge',
                        ],
                    ],
                ],
            ];
        }

        if (in_array($category, ['dressing', 'armoire'], true)) {
            return [
                'heading' => 'Avant de valider ce projet dressing',
                'description' => 'Les grandes fiches dressing rassurent sur la place reelle, la repartition interieure et le rendu final dans la chambre. Cette zone sert exactement a cela.',
                'cards' => [
                    [
                        'title' => 'Repartissez suspendu, plie et accessoires',
                        'description' => 'Un dressing convaincant ne se juge pas seulement a la largeur. Il doit organiser differemment vetements longs, chemises, chaussures, linge et sacs.',
                        'points' => [
                            'Penderie pour les longueurs et les pieces du quotidien',
                            'Tablettes pour pulls, linge et boites',
                            'Tiroirs ou zones fermees pour garder une facade nette',
                        ],
                    ],
                    [
                        'title' => 'Verifiez les degagements autour de la facade',
                        'description' => 'L impression de confort depend du passage devant le meuble, de l ouverture des portes et de la distance avec le lit ou les autres volumes.',
                        'points' => [
                            'Laisser assez d espace pour ouvrir et circuler',
                            'Tenir compte des tables de nuit et du rayon de passage',
                            'Confirmer l acces au logement avant la livraison',
                        ],
                    ],
                    [
                        'title' => 'Composez une facade qui apaise la piece',
                        'description' => 'Le rendu le plus premium vient souvent d un melange de portes, niches, miroirs et accessoires choisis avec retenue.',
                        'points' => [
                            'Alterner zones fermees et zones d acces rapide',
                            'Prevoir un miroir ou une assise si la place le permet',
                            'Garder les teintes et la quincaillerie coherentes',
                        ],
                    ],
                ],
            ];
        }

        return [
            'heading' => 'Comment tirer le meilleur de ce meuble',
            'description' => 'Au dela de la photo, une bonne fiche meuble aide a verifier la place, l usage quotidien et la facon dont le produit va s installer dans la scene complete.',
            'cards' => [
                [
                    'title' => 'Verifiez le bon emplacement',
                    'description' => 'Un meuble convainc davantage lorsqu il est choisi avec une vraie logique de circulation, de lumiere et de degagement.',
                    'points' => [
                        'Mesurer largeur, profondeur et acces',
                        'Laisser respirer le passage autour du meuble',
                        'Adapter l emplacement a la lumiere naturelle',
                    ],
                ],
                [
                    'title' => 'Pensez usage avant style',
                    'description' => 'Le meilleur rendu vient d un produit qui repond au volume a ranger, pas seulement a une silhouette attractive sur photo.',
                    'points' => [
                        'Lister ce qui doit etre cache ou visible',
                        'Verifier la frequence d usage des compartiments',
                        'Garder les zones les plus pratiques pour le quotidien',
                    ],
                ],
                [
                    'title' => 'Finalisez la mise en scene',
                    'description' => 'Quelques accessoires bien choisis suffisent a donner au meuble une presence plus complete et plus vendeuse dans la piece.',
                    'points' => [
                        'Associer miroir, lampe, cadre ou paniers',
                        'Eviter d encombrer toutes les surfaces',
                        'Conserver une palette simple et calme',
                    ],
                ],
            ],
        ];
    }

    /**
     * Resolve a set of product links for advice cards.
     *
     * @param  Collection<string, array<string, mixed>>  $links
     * @param  list<string>  $slugs
     * @return list<array<string, string>>
     */
    private function resolveGuideLinks(Collection $links, array $slugs): array
    {
        return collect($slugs)
            ->map(fn (string $slug) => $links->get($slug))
            ->filter()
            ->map(fn (array $product) => [
                'label' => $product['name'],
                'url' => $product['detailsUrl'],
            ])
            ->values()
            ->all();
    }

    /**
     * Determine whether the current visitor still has a valid preorder.
     */
    private function hasEffectivePreorder(Request $request, Product $product): bool
    {
        return $this->interactionTracker->hasPreorder($request, $product);
    }

    /**
     * Resolve the image gallery for a product page.
     *
     * @return list<string>
     */
    private function productGallery(Product $product): array
    {
        return $this->galleryManager->galleryUrls($product);
    }

    /**
     * Resolve the label of a collection.
     */
    private function labelForCollection(string $collection): string
    {
        return self::COLLECTION_LABELS[$collection] ?? 'Collection';
    }

    /**
     * Resolve the label of a product category.
     */
    private function labelForCategory(string $category): string
    {
        return self::CATEGORY_LABELS[$category] ?? ucfirst($category);
    }
}
