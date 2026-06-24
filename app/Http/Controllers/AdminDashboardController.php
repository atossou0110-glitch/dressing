<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BrowserNotificationSubscription;
use App\Models\FlashCampaign;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductReview;
use App\Models\SiteSetting;
use App\Models\SupportConversation;
use App\Support\DashboardAnalyticsService;
use App\Support\ExportService;
use App\Support\LoyaltyProgramService;
use App\Support\NewsletterAnalyticsService;
use App\Support\ProductGalleryManager;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    private const STUDY_DAYS_SESSION_KEY = 'admin.dashboard.study_days';

    /**
     * Human labels for storefront categories.
     *
     * @var array<string, string>
     */
    private const CATEGORY_LABELS = [
        'commode' => 'Commode',
        'etagere' => 'Etagere',
        'dressing' => 'Dressing',
        'armoire' => 'Armoire',
    ];

    public function __construct(
        private readonly ProductGalleryManager $galleryManager,
        private readonly NewsletterAnalyticsService $newsletterAnalytics,
        private readonly LoyaltyProgramService $loyaltyProgram,
        private readonly ExportService $exportService,
        private readonly DashboardAnalyticsService $analytics,
    ) {}

    /**
     * Display the main admin dashboard.
     */
    public function index(Request $request): View
    {
        Product::syncRequiredCatalog();

        $products = $this->productsForDashboard();
        $studyDays = $this->resolveStudyDays($request);
        $study = $this->studyReport($studyDays, $products);
        $newsletterOverview = $this->newsletterAnalytics->overview();

        return view('dashboard', [
            'overview' => $this->overviewCards($products, $newsletterOverview),
            'products' => $products,
            'productLeaders' => $this->productLeaders($products),
            'study' => $study,
            'orders' => ProductOrder::query()
                ->with('product:id,slug,code,name')
                ->latest()
                ->limit(8)
                ->get(),
            'orderOverview' => $this->orderOverview(),
            'newsletterOverview' => $newsletterOverview,
            'browserNotificationOverview' => $this->browserNotificationOverview(),
            'loyaltyLeaderboard' => $this->loyaltyProgram->leaderboard(5),
            'flashCampaigns' => FlashCampaign::query()
                ->latest('starts_at')
                ->latest('id')
                ->limit(6)
                ->get(),
            'supportConversations' => SupportConversation::query()
                ->with(['product:id,slug,code,name', 'messages'])
                ->orderByDesc('last_message_at')
                ->limit(6)
                ->get(),
            'recentReviews' => ProductReview::query()
                ->with('product:id,slug,code,name')
                ->latest()
                ->limit(8)
                ->get(),
            'settings' => [
                'whatsapp_number' => SiteSetting::value('whatsapp_number', (string) config('services.whatsapp.number')),
            ],
            'viewMap' => $this->viewMap(),
            'featureMap' => $this->featureMap($newsletterOverview),
            'chartData' => [
                'orderTrend' => $this->analytics->orderTrendData(30),
                'topProducts' => $this->analytics->topProductsData(8),
                'engagement' => $this->analytics->engagementMetrics(),
                'categoryDistribution' => $this->analytics->categoryDistribution(),
                'revenueByProduct' => $this->analytics->revenueByProduct(30),
            ],
            'recentAuditLogs' => AuditLog::query()
                ->with('user:id,name')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    /**
     * Display the multi-product management page.
     */
    public function products(): View
    {
        Product::syncRequiredCatalog();

        $products = $this->productsForDashboard();

        return view('dashboard-products', [
            'products' => $products,
            'productMedia' => $products->mapWithKeys(fn (Product $product) => [
                $product->id => ['images' => $this->galleryManager->adminImages($product)],
            ])->all(),
            'productStats' => [
                'total' => $products->count(),
                'visible' => $products->filter(fn (Product $product) => $product->isStorefrontVisible())->count(),
                'hidden' => $products->filter(fn (Product $product) => ! $product->isStorefrontVisible())->count(),
                'preorders' => (int) $products->sum('preorder_count'),
                'reviews' => (int) $products->sum(fn (Product $product) => (int) ($product->reviews_count ?? 0)),
            ],
            'categoryOptions' => self::CATEGORY_LABELS,
        ]);
    }

    /**
     * Display the dedicated product A admin page.
     */
    public function productsA(): View
    {
        return $this->dedicatedProductPage('produit-a', 'dashboard-products-a');
    }

    /**
     * Display the dedicated product B admin page.
     */
    public function productsB(): View
    {
        return $this->dedicatedProductPage('produit-b', 'dashboard-products-b');
    }

    /**
     * Export the study report as a UTF-8 CSV.
     */
    public function exportStudyReportCsv(Request $request): StreamedResponse
    {
        Product::syncRequiredCatalog();

        $days = $this->sanitizeStudyDays((int) $request->integer('days', 30));
        $study = $this->studyReport($days, $this->productsForDashboard());

        $filename = sprintf('dashboard-study-%s.csv', now()->format('Ymd-His'));

        return response()->streamDownload(function () use ($study): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['metric', 'value']);
            fputcsv($handle, ['timezone', (string) config('app.timezone')]);
            fputcsv($handle, ['days', $study['days']]);
            fputcsv($handle, ['date_start', $study['dateStart']]);
            fputcsv($handle, ['date_end', $study['dateEnd']]);
            fputcsv($handle, ['total_preorders', $study['totals']['preorders']]);
            fputcsv($handle, ['total_reviews', $study['totals']['reviews']]);
            fputcsv($handle, ['preorders_per_day', $study['totals']['preordersPerDay']]);
            fputcsv($handle, ['reviews_per_day', $study['totals']['reviewsPerDay']]);
            fputcsv($handle, ['tendance_precommandes_7j', $study['totals']['preorderTrend7d']]);
            fputcsv($handle, []);

            fputcsv($handle, ['date', 'preorders', 'reviews', 'preorders_a', 'preorders_b']);

            foreach ($study['daily'] as $row) {
                fputcsv($handle, [
                    $row['date'],
                    $row['preorders'],
                    $row['reviews'],
                    $row['preorders_a'],
                    $row['preorders_b'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['product_code', 'product_name', 'preorders', 'reviews', 'preorder_share_percent']);

            foreach ($study['products'] as $row) {
                fputcsv($handle, [
                    $row['code'],
                    $row['name'],
                    $row['preorders'],
                    $row['reviews'],
                    $row['preorderSharePercent'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Create one extra product from the dashboard.
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', 'unique:products,slug'],
            'code' => ['required', 'string', 'size:1', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(self::CATEGORY_LABELS))],
            'home_badge' => ['nullable', 'string', 'max:120'],
            'home_description' => ['nullable', 'string', 'max:500'],
            'home_price' => ['nullable', 'string', 'max:120'],
            'home_highlight' => ['nullable', 'string', 'max:255'],
            'detail_subtitle' => ['nullable', 'string', 'max:500'],
            'detail_badge' => ['nullable', 'string', 'max:160'],
            'detail_description' => ['nullable', 'string', 'max:2000'],
            'features_text' => ['nullable', 'string', 'max:2000'],
            'specifications_text' => ['nullable', 'string', 'max:2000'],
        ]);

        Product::query()->create([
            'slug' => Str::lower($validated['slug']),
            'code' => Str::upper($validated['code']),
            'name' => trim($validated['name']),
            'category' => $validated['category'],
            'content' => $this->productContentPayload($validated),
            'vote_count' => 0,
            'preorder_count' => 0,
        ]);

        return redirect()
            ->route('dashboard.products')
            ->with('status', 'Le nouveau produit a ete ajoute au dashboard.');
    }

    /**
     * Update one product and its storefront content.
     */
    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'form_product_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'in:'.implode(',', array_keys(self::CATEGORY_LABELS))],
            'preorder_count' => ['required', 'integer', 'min:0'],
            'home_badge' => ['nullable', 'string', 'max:120'],
            'home_description' => ['nullable', 'string', 'max:500'],
            'home_price' => ['nullable', 'string', 'max:120'],
            'home_highlight' => ['nullable', 'string', 'max:255'],
            'detail_subtitle' => ['nullable', 'string', 'max:500'],
            'detail_badge' => ['nullable', 'string', 'max:160'],
            'detail_description' => ['nullable', 'string', 'max:2000'],
            'features_text' => ['nullable', 'string', 'max:2000'],
            'specifications_text' => ['nullable', 'string', 'max:2000'],
        ]);

        if ((int) $validated['preorder_count'] === 0) {
            $product->preorders()->delete();
        }

        $product->forceFill([
            'name' => trim($validated['name']),
            'category' => $validated['category'] ?? $product->category,
            'preorder_count' => (int) $validated['preorder_count'],
            'content' => $this->productContentPayload($validated),
        ])->save();

        return redirect()
            ->route('dashboard.products')
            ->with('status', sprintf('Le produit %s a ete mis a jour.', $product->name));
    }

    /**
     * Reset one product interaction state.
     */
    public function resetProductCounters(Product $product): RedirectResponse
    {
        $product->votes()->delete();
        $product->preorders()->delete();

        $product->forceFill([
            'vote_count' => 0,
            'preorder_count' => 0,
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('status', sprintf('Les interactions de %s ont ete remises a zero.', $product->name));
    }

    /**
     * Upload managed images for one product.
     */
    public function storeProductImages(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,jfif', 'max:8192'],
        ]);

        $this->galleryManager->store($product, $validated['images']);

        return redirect($this->adminReturnUrl($request, 'dashboard'))
            ->with('status', sprintf('Les images de %s ont ete mises a jour.', $product->name));
    }

    /**
     * Delete one managed product image.
     */
    public function destroyProductImage(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'filename' => ['required', 'string', 'max:255'],
        ]);

        $deleted = $this->galleryManager->delete($product, $validated['filename']);

        return redirect($this->adminReturnUrl($request, 'dashboard'))
            ->with('status', $deleted
                ? sprintf('L image %s a ete supprimee.', $validated['filename'])
                : 'Impossible de supprimer cette image.');
    }

    /**
     * Update WhatsApp settings from the dashboard.
     */
    public function updateWhatsApp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_number' => ['nullable', 'string', 'max:40'],
        ]);

        $normalizedNumber = preg_replace('/\D+/', '', (string) ($validated['whatsapp_number'] ?? ''));

        SiteSetting::store('whatsapp_number', $normalizedNumber !== '' ? $normalizedNumber : null);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Le numero WhatsApp a ete enregistre.');
    }

    /**
     * Delete one review from the admin dashboard.
     */
    public function destroyReview(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'L avis a ete supprime du site.');
    }

    /**
     * Export all products to CSV.
     */
    public function exportProductsCsv(): StreamedResponse
    {
        return $this->exportService->exportProductsCsv();
    }

    /**
     * Export all orders to CSV.
     */
    public function exportOrdersCsv(): StreamedResponse
    {
        return $this->exportService->exportOrdersCsv();
    }

    /**
     * Export all clients to CSV.
     */
    public function exportClientsCsv(): StreamedResponse
    {
        return $this->exportService->exportClientsCsv();
    }

    /**
     * Export audit logs to CSV.
     */
    public function exportAuditLogsCsv(): StreamedResponse
    {
        return $this->exportService->exportAuditLogsCsv();
    }

    /**
     * Display audit logs list.
     */
    public function auditLogs(Request $request): View
    {
        $query = AuditLog::query()->with('user:id,name,email');

        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%")
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($model = $request->get('model')) {
            $query->where('model_type', $model);
        }

        $logs = $query->latest()->paginate(50);

        return view('dashboard-audit-logs', [
            'logs' => $logs,
            'actions' => AuditLog::distinct()->pluck('action')->sort()->toArray(),
            'modelTypes' => AuditLog::distinct()->pluck('model_type')->sort()->toArray(),
        ]);
    }

    /**
     * Display a dedicated product admin page.
     */
    private function dedicatedProductPage(string $slug, string $view): View
    {
        Product::syncRequiredCatalog();

        $product = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->firstOrFail();

        return view($view, [
            'product' => $product,
            'productMedia' => [
                'images' => $this->galleryManager->adminImages($product),
            ],
        ]);
    }

    /**
     * Resolve the standard product collection for admin views.
     */
    private function productsForDashboard(): Collection
    {
        return Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('code')
            ->get()
            ->filter(fn (Product $product) => $product->isStorefrontVisible())
            ->values();
    }

    /**
     * Build the content payload saved on a product.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function productContentPayload(array $validated): array
    {
        return [
            'home_badge' => trim((string) ($validated['home_badge'] ?? '')),
            'home_description' => trim((string) ($validated['home_description'] ?? '')),
            'home_price' => trim((string) ($validated['home_price'] ?? '')),
            'home_highlight' => trim((string) ($validated['home_highlight'] ?? '')),
            'detail_subtitle' => trim((string) ($validated['detail_subtitle'] ?? '')),
            'detail_badge' => trim((string) ($validated['detail_badge'] ?? '')),
            'detail_description' => trim((string) ($validated['detail_description'] ?? '')),
            'features' => $this->multilineField((string) ($validated['features_text'] ?? '')),
            'specifications' => $this->multilineField((string) ($validated['specifications_text'] ?? '')),
        ];
    }

    /**
     * Convert a textarea payload into a clean list.
     *
     * @return list<string>
     */
    private function multilineField(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Resolve and store the active study period.
     */
    private function resolveStudyDays(Request $request): int
    {
        if ($request->filled('study_days')) {
            $days = $this->sanitizeStudyDays((int) $request->integer('study_days'));
            $request->session()->put(self::STUDY_DAYS_SESSION_KEY, $days);

            return $days;
        }

        return $this->sanitizeStudyDays((int) $request->session()->get(self::STUDY_DAYS_SESSION_KEY, 30));
    }

    /**
     * Keep study ranges inside a safe admin window.
     */
    private function sanitizeStudyDays(int $days): int
    {
        return max(7, min(365, $days));
    }

    /**
     * Build high-level overview cards for the dashboard.
     *
     * @param  array<string, mixed>  $newsletterOverview
     * @return list<array<string, mixed>>
     */
    private function overviewCards(Collection $products, array $newsletterOverview): array
    {
        $visibleProducts = $products->filter(fn (Product $product) => $product->isStorefrontVisible());
        $paidStatuses = ['approved', 'transferred'];

        return [
            [
                'label' => 'Produits actifs',
                'value' => $products->count(),
                'copy' => sprintf('%d visibles, %d masques.', $visibleProducts->count(), $products->count() - $visibleProducts->count()),
            ],
            [
                'label' => 'Precommandes',
                'value' => (int) $products->sum('preorder_count'),
                'copy' => 'Demandes WhatsApp suivies depuis le catalogue.',
            ],
            [
                'label' => 'Avis',
                'value' => ProductReview::query()->count(),
                'copy' => sprintf('Note moyenne %s/5.', number_format((float) (ProductReview::query()->avg('rating') ?? 0), 1)),
            ],
            [
                'label' => 'Commandes',
                'value' => ProductOrder::query()->count(),
                'copy' => sprintf('%d payees ou transferees.', ProductOrder::query()->whereIn('status', $paidStatuses)->count()),
            ],
            [
                'label' => 'CA encaisse',
                'value' => number_format((int) ProductOrder::query()->whereIn('status', $paidStatuses)->sum('amount'), 0, ',', ' ').' FCFA',
                'copy' => 'Montant confirme via le checkout.',
            ],
            [
                'label' => 'Newsletter',
                'value' => $newsletterOverview['subscribers'],
                'copy' => sprintf('%d conversions, %d FCFA de revenus attribues.', $newsletterOverview['conversions'], $newsletterOverview['revenue']),
            ],
        ];
    }

    /**
     * Build the dashboard order summary.
     *
     * @return array<string, mixed>
     */
    private function orderOverview(): array
    {
        $paidStatuses = ['approved', 'transferred'];

        return [
            'total' => ProductOrder::query()->count(),
            'pending' => ProductOrder::query()->where('status', 'pending')->count(),
            'paid' => ProductOrder::query()->whereIn('status', $paidStatuses)->count(),
            'revenue' => (int) ProductOrder::query()->whereIn('status', $paidStatuses)->sum('amount'),
        ];
    }

    /**
     * Build browser notification consent metrics.
     *
     * @return array<string, mixed>
     */
    private function browserNotificationOverview(): array
    {
        return [
            'granted' => BrowserNotificationSubscription::query()->where('permission', 'granted')->count(),
            'denied' => BrowserNotificationSubscription::query()->where('permission', 'denied')->count(),
            'default' => BrowserNotificationSubscription::query()->where('permission', 'default')->count(),
            'lastSeenAt' => BrowserNotificationSubscription::query()->max('last_seen_at'),
        ];
    }

    /**
     * Resolve quick product leaders for the overview page.
     *
     * @return list<array<string, mixed>>
     */
    private function productLeaders(Collection $products): array
    {
        return $products
            ->sortByDesc(fn (Product $product) => ((int) $product->preorder_count * 1000)
                + (int) ($product->reviews_count ?? 0))
            ->take(6)
            ->map(fn (Product $product) => [
                'name' => $product->name,
                'code' => $product->code,
                'category' => self::CATEGORY_LABELS[$product->category] ?? ucfirst((string) $product->category),
                'preorders' => (int) $product->preorder_count,
                'reviews' => (int) ($product->reviews_count ?? 0),
                'visible' => $product->isStorefrontVisible(),
                'dashboardUrl' => route('dashboard.products').'#product-editor-'.$product->id,
                'publicUrl' => route('products.show', $product),
            ])
            ->values()
            ->all();
    }

    /**
     * Build the site-map cards used in the dashboard analysis area.
     *
     * @return list<array<string, string>>
     */
    private function viewMap(): array
    {
        return [
            [
                'name' => 'Catalogue principal',
                'path' => '/ et /catalog',
                'purpose' => 'Accueil commercial avec hero, recherche, filtres, collections, avis, newsletter et support.',
            ],
            [
                'name' => 'Solutions King',
                'path' => '/dr-dressing',
                'purpose' => 'Landing specialisee sur les produits dressing et armoire avec mini-categories et contenus d aide.',
            ],
            [
                'name' => 'Fiche produit',
                'path' => '/produit/{slug}',
                'purpose' => 'Detail produit, galerie, avis, conseils, produits suggeres et call-to-action d achat.',
            ],
            [
                'name' => 'Checkout',
                'path' => '/produit/{slug}/checkout',
                'purpose' => 'Tunnel de commande avec collecte client puis redirection securisee FedaPay.',
            ],
            [
                'name' => 'Suivi commande',
                'path' => '/commandes/{reference}',
                'purpose' => 'Suivi temps reel du statut de paiement et verification post-checkout.',
            ],
            [
                'name' => 'Auth et profil',
                'path' => '/login, /register, /profile',
                'purpose' => 'Authentification Laravel Breeze, verification email et gestion du profil admin.',
            ],
        ];
    }

    /**
     * Build a capability map for the dashboard analysis area.
     *
     * @param  array<string, mixed>  $newsletterOverview
     * @return list<array<string, string>>
     */
    private function featureMap(array $newsletterOverview): array
    {
        return [
            [
                'name' => 'Catalogue dynamique',
                'copy' => 'Recherche texte, filtres par categorie, collection et budget, plus mise en avant des produits visibles.',
            ],
            [
                'name' => 'Precommandes et suivi client',
                'copy' => 'Demandes WhatsApp trackees par produit, avec lecture admin des performances catalogue.',
            ],
            [
                'name' => 'E-commerce',
                'copy' => 'Commande, paiement FedaPay, suivi de transaction, webhook et retour de paiement.',
            ],
            [
                'name' => 'Engagement',
                'copy' => sprintf('Newsletter (%d inscrits), campagnes flash navigateur et moderation des avis.', $newsletterOverview['subscribers']),
            ],
            [
                'name' => 'Support assiste',
                'copy' => 'Widget conversationnel avec tri des demandes, suggestions produit, dimensions, livraison et escalation humaine.',
            ],
            [
                'name' => 'Fidelisation',
                'copy' => 'Points, tiers clients et attribution de conversions newsletter et commandes payees.',
            ],
        ];
    }

    /**
     * Build the daily study report used on screen and in CSV export.
     *
     * @return array<string, mixed>
     */
    private function studyReport(int $days, Collection $products): array
    {
        $timezone = (string) config('app.timezone', 'UTC');
        $end = now()->setTimezone($timezone)->endOfDay()->toImmutable();
        $start = $end->subDays($days - 1)->startOfDay();

        $daily = collect(range(0, $days - 1))
            ->mapWithKeys(fn (int $offset) => [
                $start->addDays($offset)->format('Y-m-d') => [
                    'date' => $start->addDays($offset)->format('Y-m-d'),
                    'preorders' => 0,
                    'reviews' => 0,
                    'preorders_a' => 0,
                    'preorders_b' => 0,
                ],
            ])
            ->all();

        $productRows = $products
            ->mapWithKeys(fn (Product $product) => [
                $product->id => [
                    'code' => $product->code,
                    'name' => $product->name,
                    'preorders' => 0,
                    'reviews' => 0,
                    'preorderSharePercent' => 0.0,
                    'publicUrl' => route('products.show', $product),
                ],
            ])
            ->all();

        $startUtc = $start->setTimezone('UTC');

        $preorderRows = DB::table('product_preorders')
            ->join('products', 'products.id', '=', 'product_preorders.product_id')
            ->where('product_preorders.created_at', '>=', $startUtc)
            ->get([
                'product_preorders.product_id',
                'product_preorders.created_at',
                'products.code',
            ]);

        foreach ($preorderRows as $row) {
            $dateKey = CarbonImmutable::parse((string) $row->created_at)->setTimezone($timezone)->format('Y-m-d');

            if (! isset($daily[$dateKey])) {
                continue;
            }

            $daily[$dateKey]['preorders']++;

            if (strtolower((string) $row->code) === 'a') {
                $daily[$dateKey]['preorders_a']++;
            }

            if (strtolower((string) $row->code) === 'b') {
                $daily[$dateKey]['preorders_b']++;
            }

            if (isset($productRows[$row->product_id])) {
                $productRows[$row->product_id]['preorders']++;
            }
        }

        $reviewRows = DB::table('product_reviews')
            ->where('created_at', '>=', $startUtc)
            ->get([
                'product_id',
                'created_at',
            ]);

        foreach ($reviewRows as $row) {
            $dateKey = CarbonImmutable::parse((string) $row->created_at)->setTimezone($timezone)->format('Y-m-d');

            if (! isset($daily[$dateKey])) {
                continue;
            }

            $daily[$dateKey]['reviews']++;

            if (isset($productRows[$row->product_id])) {
                $productRows[$row->product_id]['reviews']++;
            }
        }

        $dailyRows = array_values($daily);
        $totalPreorders = array_sum(array_column($dailyRows, 'preorders'));
        $totalReviews = array_sum(array_column($dailyRows, 'reviews'));
        $preorderTrend7d = $this->preorderTrend($dailyRows);

        $productRows = collect($productRows)
            ->map(function (array $row) use ($totalPreorders): array {
                $row['preorderSharePercent'] = $totalPreorders > 0
                    ? round(($row['preorders'] / $totalPreorders) * 100, 1)
                    : 0.0;

                return $row;
            })
            ->sortByDesc(fn (array $row) => ($row['preorders'] * 1000) + $row['reviews'])
            ->values()
            ->all();

        return [
            'days' => $days,
            'dateStart' => $start->format('Y-m-d'),
            'dateEnd' => $end->format('Y-m-d'),
            'daily' => $dailyRows,
            'products' => $productRows,
            'totals' => [
                'preorders' => $totalPreorders,
                'reviews' => $totalReviews,
                'preordersPerDay' => round($totalPreorders / max($days, 1), 1),
                'reviewsPerDay' => round($totalReviews / max($days, 1), 1),
                'preorderTrend7d' => $preorderTrend7d,
            ],
        ];
    }

    /**
     * Compute the preorder trend between the last 7 days and the 7 before.
     *
     * @param  list<array<string, mixed>>  $dailyRows
     */
    private function preorderTrend(array $dailyRows): float
    {
        $rows = collect($dailyRows);
        $current = (int) $rows->take(-7)->sum('preorders');
        $previous = (int) $rows->slice(max(0, $rows->count() - 14), min(7, max(0, $rows->count() - 7)))->sum('preorders');

        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Resolve the best return URL inside the admin area.
     */
    private function adminReturnUrl(Request $request, string $fallbackRoute): string
    {
        $referer = (string) $request->headers->get('referer', '');
        $knownTargets = [
            route('dashboard.products'),
            route('dashboard'),
        ];

        foreach ($knownTargets as $target) {
            if ($referer !== '' && str_starts_with($referer, $target)) {
                return $target;
            }
        }

        return route($fallbackRoute);
    }
}
