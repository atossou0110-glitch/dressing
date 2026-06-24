<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    use HasFactory;

    /**
     * Cache whether the optional category column exists.
     */
    private static ?bool $hasCategoryColumn = null;

    /**
     * Older seeded product names that should be refreshed to the new catalog.
     *
     * @var array<string, string>
     */
    private const LEGACY_PRODUCT_NAMES = [
        'produit-a' => 'commode',
        'produit-b' => 'Meuble tiroirs empilables',
        'produit-c' => 'Tagere murale premium',
    ];

    /**
     * Required catalog entries for the storefront.
     *
     * @var array<string, array{slug: string, code: string, category: string, default_name: string, default_content: array<string, mixed>}>
     */
    private const REQUIRED_CATALOG = [
        'produit-a' => [
            'slug' => 'produit-a',
            'code' => 'A',
            'category' => 'commode',
            'default_name' => 'Commode Ivoire Signature',
            'default_content' => [
                'home_badge' => 'Format chambre',
                'home_description' => 'Commode ivoire a facade cannelee avec grands tiroirs et finition haut de gamme.',
                'home_price' => '349 000 FCFA',
                'home_highlight' => 'Chaque chose a sa place sans encombrer la piece.',
                'detail_subtitle' => 'Une commode premium pour structurer la chambre, l entree ou un salon raffine.',
                'detail_badge' => 'Rangement tiroirs - 349 000 FCFA',
                'detail_description' => 'Cette commode ivoire combine une presence elegante, des volumes de rangement genereux et une finition facile a integrer dans des interieurs modernes.',
                'features' => [
                    'Grands tiroirs pour linge, accessoires et petits objets',
                    'Facade claire qui apaise visuellement la piece',
                    'Plateau stable pour miroir, lampe ou decoration',
                ],
                'specifications' => [
                    'Dimensions : 160 x 82 x 48 cm',
                    'Materiaux : MDF laque et bois renforce',
                    'Coloris : ivoire doux et finition sable',
                    'Utilisation : chambre, entree, salon',
                ],
            ],
        ],
        'produit-b' => [
            'slug' => 'produit-b',
            'code' => 'B',
            'category' => 'etagere',
            'default_name' => 'Etagere Murale Atelier',
            'default_content' => [
                'home_badge' => 'Mur optimise',
                'home_description' => 'Etagere murale au style atelier pour mettre en valeur sacs, livres et objets deco.',
                'home_price' => '189 000 FCFA',
                'home_highlight' => 'Exploite la hauteur au lieu de manger le sol.',
                'detail_subtitle' => 'Une etagere graphique pour exploiter la hauteur de vos murs avec style.',
                'detail_badge' => 'Etagere murale - 189 000 FCFA',
                'detail_description' => 'L etagere Murale Atelier organise les essentiels du quotidien tout en apportant une ligne architecturale au decor.',
                'features' => [
                    'Cinq niveaux pour chaussures, sacs, livres et paniers',
                    'Structure verticale ideale pour petits espaces',
                    'Ligne atelier facile a associer a une chambre ou entree',
                ],
                'specifications' => [
                    'Dimensions : 90 x 190 x 30 cm',
                    'Materiaux : bois stratifie et acier peint',
                    'Coloris : ivoire et noir satine',
                    'Capacite : 5 niveaux de rangement',
                ],
            ],
        ],
        'produit-c' => [
            'slug' => 'produit-c',
            'code' => 'C',
            'category' => 'commode',
            'default_name' => 'Commode Basse Horizon',
            'default_content' => [
                'home_badge' => 'Grand volume',
                'home_description' => 'Commode basse tres large pour linge de maison, vetements et accessoires volumineux.',
                'home_price' => '279 000 FCFA',
                'home_highlight' => 'Range large tout en gardant une facade discrete.',
                'detail_subtitle' => 'Une silhouette basse et horizontale pour les pieces qui demandent du rangement discret.',
                'detail_badge' => 'Commode basse - 279 000 FCFA',
                'detail_description' => 'La Commode Basse Horizon offre un rangement efficace avec une ligne plus etiree et un grand volume de stockage.',
                'features' => [
                    'Six tiroirs pour separer vetements et linge',
                    'Format bas compatible miroir ou television',
                    'Rendu sobre pour une piece plus ordonnee',
                ],
                'specifications' => [
                    'Dimensions : 180 x 68 x 46 cm',
                    'Materiaux : panneau haute densite et bois',
                    'Coloris : sable clair et ivoire',
                    'Capacite : 6 grands tiroirs',
                ],
            ],
        ],
        'produit-d' => [
            'slug' => 'produit-d',
            'code' => 'D',
            'category' => 'etagere',
            'default_name' => 'Etagere Colonne Nacre',
            'default_content' => [
                'home_badge' => 'Angle utile',
                'home_description' => 'Colonne ouverte pour boites, paniers et linge avec un style minimaliste et lumineux.',
                'home_price' => '229 000 FCFA',
                'home_highlight' => 'Transforme un angle perdu en zone de rangement.',
                'detail_subtitle' => 'Une colonne de rangement verticale qui donne du rythme sans encombrer la piece.',
                'detail_badge' => 'Colonne rangement - 229 000 FCFA',
                'detail_description' => 'Cette etagere colonne a ete pensee pour les espaces etroits ou chaque centimetre compte.',
                'features' => [
                    'Six niches ouvertes faciles a organiser',
                    'Faible largeur pour couloir, studio ou chambre',
                    'Compatible boites, paniers et objets visibles',
                ],
                'specifications' => [
                    'Dimensions : 58 x 205 x 34 cm',
                    'Materiaux : melamine premium',
                    'Coloris : nacre et bois clair',
                    'Capacite : 6 niches ouvertes',
                ],
            ],
        ],
        'produit-e' => [
            'slug' => 'produit-e',
            'code' => 'E',
            'category' => 'dressing',
            'default_name' => 'Dressing Ivoire Modulable',
            'default_content' => [
                'home_badge' => 'Dressing complet',
                'home_description' => 'Grand dressing modulable avec penderie, tiroirs et niches pour un rangement sur mesure.',
                'home_price' => '899 000 FCFA',
                'home_highlight' => 'Une seule composition pour vetements, chaussures et sacs.',
                'detail_subtitle' => 'Le dressing de reference pour organiser chaussures, chemises, sacs et linge dans un seul ensemble.',
                'detail_badge' => 'Dressing modulable - 899 000 FCFA',
                'detail_description' => 'Le Dressing Ivoire Modulable rassemble penderies, niches ouvertes et tiroirs afin de construire un espace aussi beau qu efficace.',
                'features' => [
                    'Zones penderie pour chemises, robes et vestes',
                    'Tiroirs fermes pour garder une facade propre',
                    'Niches ouvertes pour chaussures et accessoires',
                ],
                'specifications' => [
                    'Dimensions : 280 x 240 x 60 cm',
                    'Materiaux : bois technique et quincaillerie renforcee',
                    'Coloris : ivoire, beige et inserts bois',
                    'Capacite : 3 zones penderie + 8 niches + 6 tiroirs',
                ],
            ],
        ],
        'produit-f' => [
            'slug' => 'produit-f',
            'code' => 'F',
            'category' => 'armoire',
            'default_name' => 'Armoire Atelier Noyer',
            'default_content' => [
                'home_badge' => 'Facade fermee',
                'home_description' => 'Armoire compacte au look atelier pour les chambres ou les studios exigeants.',
                'home_price' => '649 000 FCFA',
                'home_highlight' => 'Cache le visuel charge sans perdre l acces pratique.',
                'detail_subtitle' => 'Une armoire pratique pour garder un rangement ferme, discret et elegant.',
                'detail_badge' => 'Armoire organisee - 649 000 FCFA',
                'detail_description' => 'Avec ses portes pleines et son interieur organise, cette armoire protege les vetements, le linge et les accessoires.',
                'features' => [
                    'Portes pleines pour masquer le desordre visuel',
                    'Penderie integree pour les pieces du quotidien',
                    'Tablettes pour linge, sacs et boites',
                ],
                'specifications' => [
                    'Dimensions : 140 x 220 x 58 cm',
                    'Materiaux : bois noyer et panneaux renforces',
                    'Coloris : noyer fonce et noir doux',
                    'Capacite : 2 portes, 1 penderie, 5 tablettes',
                ],
            ],
        ],
        'produit-g' => [
            'slug' => 'produit-g',
            'code' => 'G',
            'category' => 'armoire',
            'default_name' => 'Placard Coulissant Chene Gris',
            'default_content' => [
                'home_badge' => 'Portes coulissantes',
                'home_description' => 'Placard coulissant compact pour optimiser une chambre sans perdre de passage.',
                'home_price' => '749 000 FCFA',
                'home_highlight' => 'Ideal quand les portes battantes prennent trop de place.',
                'detail_subtitle' => 'Un placard coulissant pour les chambres qui doivent rester fluides et bien rangees.',
                'detail_badge' => 'Placard coulissant - 749 000 FCFA',
                'detail_description' => 'Le Placard Coulissant Chene Gris libere la circulation tout en gardant une facade propre et une vraie capacite de rangement.',
                'features' => [
                    'Portes coulissantes pour economiser le recul',
                    'Penderie et tablettes pour separer vetements et linge',
                    'Facade chene gris facile a integrer dans une chambre moderne',
                ],
                'specifications' => [
                    'Dimensions : 180 x 225 x 60 cm',
                    'Materiaux : panneaux bois renforces et rails metal',
                    'Coloris : chene clair, gris doux et profils noirs',
                    'Capacite : 2 portes coulissantes, penderie, 6 tablettes',
                ],
            ],
        ],
        'produit-h' => [
            'slug' => 'produit-h',
            'code' => 'H',
            'category' => 'dressing',
            'default_name' => 'Dressing Loft 6 Portes',
            'default_content' => [
                'home_badge' => 'Grande capacite',
                'home_description' => 'Dressing grande capacite avec portes et niches pour les besoins d une famille.',
                'home_price' => '1 190 000 FCFA',
                'home_highlight' => 'Centralise toute la garde-robe derriere une facade nette.',
                'detail_subtitle' => 'Une composition ample pour centraliser toute la garde-robe dans une seule facade.',
                'detail_badge' => 'Dressing familial - 1 190 000 FCFA',
                'detail_description' => 'Le Dressing Loft 6 Portes reunit une facade ordonnee et une tres forte capacite de stockage.',
                'features' => [
                    'Six portes pour separer les zones de rangement',
                    'Deux penderies pour vetements longs et courts',
                    'Tiroirs profonds pour accessoires et linge',
                ],
                'specifications' => [
                    'Dimensions : 320 x 245 x 62 cm',
                    'Materiaux : bois technique premium et quincaillerie soft-close',
                    'Coloris : ivoire mat et inserts bronze',
                    'Capacite : 6 portes, 4 tiroirs, 2 penderies',
                ],
            ],
        ],
    ];

    /**
     * Collection categories used in the storefront.
     *
     * @var array<string, list<string>>
     */
    private const COLLECTION_CATEGORIES = [
        'rangement' => ['commode', 'etagere'],
        'dressing' => ['dressing', 'armoire'],
    ];

    /**
     * Base content shape used by dashboard-managed products.
     *
     * @var array<string, mixed>
     */
    private const BASE_CONTENT_TEMPLATE = [
        'home_badge' => '',
        'home_description' => '',
        'home_price' => '',
        'home_highlight' => '',
        'detail_subtitle' => '',
        'detail_badge' => '',
        'detail_description' => '',
        'features' => [],
        'specifications' => [],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'code',
        'category',
        'name',
        'content',
        'vote_count',
        'preorder_count',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Resolve the primary image URL for the product.
     */
    public function imageUrl(): ?string
    {
        $galleryManager = app(\App\Support\ProductGalleryManager::class);
        $urls = $galleryManager->galleryUrls($this);

        return $urls[0] ?? null;
    }

    /**
     * Reviews left on the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Votes left on the product.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ProductVote::class);
    }

    /**
     * Preorders left on the product.
     */
    public function preorders(): HasMany
    {
        return $this->hasMany(ProductPreorder::class);
    }

    /**
     * UGC submissions attached to the product.
     */
    public function ugcSubmissions(): HasMany
    {
        return $this->hasMany(UgcSubmission::class);
    }

    /**
     * Resolve a numeric checkout amount from the storefront price.
     */
    public function checkoutAmount(): ?int
    {
        $price = (string) ($this->resolvedContent()['home_price'] ?? '');
        $digits = preg_replace('/\D+/', '', $price);

        if ($digits === null || $digits === '') {
            return null;
        }

        return (int) $digits;
    }

    /**
     * Resolve the checkout currency used by the storefront.
     */
    public function checkoutCurrency(): string
    {
        return 'XOF';
    }

    /**
     * Ensure the storefront products always exist.
     */
    public static function syncRequiredCatalog(): void
    {
        $hasCategoryColumn = self::hasCategoryColumn();

        foreach (self::REQUIRED_CATALOG as $slug => $defaults) {
            $product = self::query()->where('slug', $slug)->first();

            if ($product === null) {
                $attributes = [
                    'slug' => $defaults['slug'],
                    'code' => $defaults['code'],
                    'name' => $defaults['default_name'],
                    'content' => $defaults['default_content'],
                    'vote_count' => 0,
                    'preorder_count' => 0,
                ];

                if ($hasCategoryColumn) {
                    $attributes['category'] = $defaults['category'];
                }

                self::query()->create($attributes);

                continue;
            }

            $dirty = false;
            $legacyName = self::LEGACY_PRODUCT_NAMES[$slug] ?? null;
            $originalName = trim((string) $product->name);

            if ($product->code !== $defaults['code']) {
                $product->code = $defaults['code'];
                $dirty = true;
            }

            if ($originalName === '' || $originalName === $legacyName) {
                $product->name = $defaults['default_name'];
                $dirty = true;
            }

            if ($hasCategoryColumn && $product->getRawOriginal('category') !== $defaults['category']) {
                $product->category = $defaults['category'];
                $dirty = true;
            }

            $currentContent = is_array($product->content) ? $product->content : [];
            $usesLegacyCurrency = str_contains(strtoupper((string) ($currentContent['home_price'] ?? '')), 'EUR');
            $shouldResetLegacyContent = $originalName !== '' && $originalName === $legacyName && $usesLegacyCurrency;

            $resolvedContent = self::mergeContentDefaults(
                $defaults['default_content'],
                $shouldResetLegacyContent ? [] : $currentContent,
            );

            if ($product->content !== $resolvedContent) {
                $product->content = $resolvedContent;
                $dirty = true;
            }

            if ($dirty) {
                $product->save();
            }
        }
    }

    /**
     * Resolve a catalog product, recreating the base record if needed.
     */
    public static function resolveCatalogProduct(string $slug): ?self
    {
        if (! array_key_exists($slug, self::REQUIRED_CATALOG)) {
            return self::query()->where('slug', $slug)->first();
        }

        self::syncRequiredCatalog();

        return self::query()->where('slug', $slug)->first();
    }

    /**
     * Resolve the editable storefront content for this product.
     *
     * @return array<string, mixed>
     */
    public function resolvedContent(): array
    {
        $defaults = array_replace_recursive(
            self::BASE_CONTENT_TEMPLATE,
            self::REQUIRED_CATALOG[$this->slug]['default_content'] ?? [],
        );
        $content = is_array($this->content) ? $this->content : [];

        return self::mergeContentDefaults($defaults, $content);
    }

    /**
     * Resolve the collection key used by the storefront.
     */
    public function storefrontCollection(): string
    {
        return self::collectionForCategory($this->resolvedCategory());
    }

    /**
     * Provide a safe category value even if the database column is missing.
     */
    public function getCategoryAttribute($value): string
    {
        $resolved = trim((string) $value);

        if ($resolved !== '') {
            return $resolved;
        }

        return $this->resolvedCategory();
    }

    /**
     * Resolve the collection key for a category.
     */
    public static function collectionForCategory(string $category): string
    {
        foreach (self::COLLECTION_CATEGORIES as $collection => $categories) {
            if (in_array($category, $categories, true)) {
                return $collection;
            }
        }

        return 'rangement';
    }

    /**
     * Return the categories attached to a collection.
     *
     * @return list<string>
     */
    public static function categoriesForCollection(string $collection): array
    {
        return self::COLLECTION_CATEGORIES[$collection] ?? [];
    }

    /**
     * Return categories visible on the public storefront.
     *
     * @return list<string>
     */
    public static function visibleStorefrontCategories(): array
    {
        return array_values(array_unique(array_merge(...array_values(self::COLLECTION_CATEGORIES))));
    }

    /**
     * Return categories hidden from the public storefront.
     *
     * @return list<string>
     */
    public static function hiddenStorefrontCategories(): array
    {
        return [];
    }

    /**
     * Tell whether this product is currently visible on the public storefront.
     */
    public function isStorefrontVisible(): bool
    {
        return in_array($this->resolvedCategory(), self::visibleStorefrontCategories(), true);
    }

    /**
     * Return an empty content template for new dashboard-created products.
     *
     * @return array<string, mixed>
     */
    public static function baseContentTemplate(): array
    {
        return self::BASE_CONTENT_TEMPLATE;
    }

    /**
     * Resolve the fallback category from the current record or required catalog map.
     */
    private function resolvedCategory(): string
    {
        $storedCategory = trim((string) $this->getRawOriginal('category'));

        if ($storedCategory !== '') {
            return $storedCategory;
        }

        return self::REQUIRED_CATALOG[$this->slug]['category'] ?? 'commode';
    }

    /**
     * Check whether the products table currently has the category column.
     */
    private static function hasCategoryColumn(): bool
    {
        if (self::$hasCategoryColumn === null) {
            self::$hasCategoryColumn = Schema::hasColumn('products', 'category');
        }

        return self::$hasCategoryColumn;
    }

    /**
     * Merge saved content with required defaults.
     *
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private static function mergeContentDefaults(array $defaults, array $content): array
    {
        $merged = array_replace_recursive($defaults, array_intersect_key($content, $defaults));

        foreach (['features', 'specifications'] as $listKey) {
            $merged[$listKey] = array_values(array_filter(
                array_map('trim', (array) ($merged[$listKey] ?? [])),
                fn (string $line) => $line !== '',
            ));
        }

        foreach ([
            'home_badge',
            'home_description',
            'home_price',
            'home_highlight',
            'detail_subtitle',
            'detail_badge',
            'detail_description',
        ] as $key) {
            $merged[$key] = trim((string) ($merged[$key] ?? ''));
        }

        return $merged;
    }
}
