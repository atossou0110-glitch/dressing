<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductPreorder;
use App\Models\ProductReview;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardAnalyticsService
{
    public function orderTrendData(int $days = 30): array
    {
        $data = ProductOrder::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as revenue')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->date => [
                    'orders' => $item->count,
                    'revenue' => (float) $item->revenue,
                ],
            ])
            ->toArray();

        $dates = collect(range(0, $days - 1))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'))->reverse();

        return [
            'labels' => $dates->values()->toArray(),
            'orders' => $dates->map(fn ($date) => $data[$date]['orders'] ?? 0)->values()->toArray(),
            'revenue' => $dates->map(fn ($date) => $data[$date]['revenue'] ?? 0)->values()->toArray(),
        ];
    }

    public function topProductsData(int $limit = 10): array
    {
        $products = Product::query()
            ->withCount('reviews')
            ->orderByDesc('preorder_count')
            ->get()
            ->filter(fn (Product $product) => $product->isStorefrontVisible())
            ->take($limit)
            ->values();

        return [
            'labels' => $products->pluck('name')->toArray(),
            'preorders' => $products->pluck('preorder_count')->toArray(),
            'reviews' => $products->pluck('reviews_count')->toArray(),
            'codes' => $products->pluck('code')->toArray(),
        ];
    }

    public function engagementMetrics(): array
    {
        $lastMonth = now()->subMonth();

        return [
            'ordersMonth' => ProductOrder::where('created_at', '>=', $lastMonth)->count(),
            'preordersMonth' => ProductPreorder::where('created_at', '>=', $lastMonth)->count(),
            'reviewsMonth' => ProductReview::where('created_at', '>=', $lastMonth)->count(),
            'ordersTrend' => $this->calculateTrend(
                ProductOrder::where('created_at', '>=', $lastMonth)->count(),
                ProductOrder::whereBetween('created_at', [
                    now()->subMonths(2),
                    now()->subMonth(),
                ])->count(),
            ),
            'preordersTrend' => $this->calculateTrend(
                ProductPreorder::where('created_at', '>=', $lastMonth)->count(),
                ProductPreorder::whereBetween('created_at', [
                    now()->subMonths(2),
                    now()->subMonth(),
                ])->count(),
            ),
        ];
    }

    public function categoryDistribution(): array
    {
        $distribution = Product::query()
            ->selectRaw('category, COUNT(*) as count, SUM(preorder_count) as preorders')
            ->groupBy('category')
            ->get();

        return [
            'labels' => $distribution->pluck('category')->map(fn ($cat) => ucfirst($cat))->toArray(),
            'products' => $distribution->pluck('count')->toArray(),
            'preorders' => $distribution->pluck('preorders')->toArray(),
        ];
    }

    public function revenueByProduct(int $days = 30): array
    {
        $revenue = ProductOrder::query()
            ->with('product:id,code,name')
            ->where('created_at', '>=', now()->subDays($days))
            ->get()
            ->groupBy('product.code')
            ->map(fn (Collection $orders) => $orders->sum('amount'))
            ->sort()
            ->reverse();

        return [
            'labels' => $revenue->keys()->toArray(),
            'values' => $revenue->values()->map(fn ($val) => (float) $val)->toArray(),
        ];
    }

    public function conversionFunnelData(): array
    {
        $totalVisitors = 1000; // Placeholder - à remplacer par tracked visitors
        $productViews = Product::sum('view_count') ?? 0;
        $preorders = ProductPreorder::count();
        $orders = ProductOrder::count();

        return [
            'stages' => ['Visiteurs', 'Vues Produits', 'Précommandes', 'Commandes'],
            'values' => [
                $totalVisitors,
                $productViews,
                $preorders,
                $orders,
            ],
            'conversionRates' => [
                ($productViews / $totalVisitors * 100),
                ($preorders / $productViews * 100),
                ($orders / $preorders * 100),
            ],
        ];
    }

    private function calculateTrend(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }
}
