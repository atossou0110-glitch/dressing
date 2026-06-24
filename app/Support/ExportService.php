<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportProductsCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="produits-'.now()->format('Y-m-d_His').'.csv"',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, "%s\n", implode(',', [
                'ID',
                'Code',
                'Nom',
                'Slug',
                'Catégorie',
                'Prix (FCFA)',
                'Stock',
                'SKU',
                'Poids (kg)',
                'Visible',
                'Featured',
                'Précommandes',
                'Avis',
                'Notes moyennes',
            ]));

            Product::query()
                ->orderBy('code')
                ->chunk(100, function (Collection $products) use ($handle) {
                    foreach ($products as $product) {
                        fputcsv($handle, [
                            $product->id,
                            $product->code,
                            $product->name,
                            $product->slug,
                            $product->category,
                            $product->content['price'] ?? '',
                            $product->stock_quantity,
                            $product->sku,
                            $product->weight_kg,
                            $product->isStorefrontVisible() ? 'Oui' : 'Non',
                            $product->is_featured ? 'Oui' : 'Non',
                            $product->preorder_count ?? 0,
                            $product->reviews_count ?? 0,
                            number_format($product->average_rating ?? 0, 2),
                        ]);
                    }
                });

            fclose($handle);
        }, 'produits-'.now()->format('Y-m-d_His').'.csv', $headers);
    }

    public function exportOrdersCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="commandes-'.now()->format('Y-m-d_His').'.csv"',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, "%s\n", implode(',', [
                'ID Commande',
                'Produit',
                'Prénom Client',
                'Nom Client',
                'Email',
                'Téléphone',
                'Montant (XOF)',
                'Statut Paiement',
                'Adresse Livraison',
                'Date',
            ]));

            ProductOrder::query()
                ->with('product:id,name')
                ->latest()
                ->chunk(100, function (Collection $orders) use ($handle) {
                    foreach ($orders as $order) {
                        fputcsv($handle, [
                            $order->id,
                            $order->product->name,
                            $order->customer_first_name ?? '',
                            $order->customer_last_name ?? '',
                            $order->customer_email ?? '',
                            $order->customer_phone ?? '',
                            $order->amount ?? 0,
                            $order->status ?? 'pending',
                            $order->customer_address ?? '',
                            $order->created_at->format('Y-m-d H:i'),
                        ]);
                    }
                });

            fclose($handle);
        }, 'commandes-'.now()->format('Y-m-d_His').'.csv', $headers);
    }

    public function exportClientsCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clients-'.now()->format('Y-m-d_His').'.csv"',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, "%s\n", implode(',', [
                'Email',
                'Inscrit Newsletter',
                'Date Inscription',
                'Commandes',
                'Montant Total (XOF)',
            ]));

            $subscribers = NewsletterSubscription::query()
                ->select('email')
                ->distinct()
                ->get();

            foreach ($subscribers as $subscriber) {
                $orders = ProductOrder::where('customer_email', $subscriber->email)->get();
                fputcsv($handle, [
                    $subscriber->email,
                    'Oui',
                    $subscriber->created_at?->format('Y-m-d') ?? '',
                    $orders->count(),
                    $orders->sum('amount'),
                ]);
            }

            fclose($handle);
        }, 'clients-'.now()->format('Y-m-d_His').'.csv', $headers);
    }

    public function exportAuditLogsCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit-'.now()->format('Y-m-d_His').'.csv"',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, "%s\n", implode(',', [
                'Date/Heure',
                'Admin',
                'Action',
                'Modèle',
                'ID',
                'Description',
                'Adresse IP',
            ]));

            AuditLog::query()
                ->with('user:id,name,email')
                ->latest()
                ->chunk(100, function (Collection $logs) use ($handle) {
                    foreach ($logs as $log) {
                        fputcsv($handle, [
                            $log->created_at->format('Y-m-d H:i:s'),
                            $log->user?->name ?? 'Unknown',
                            strtoupper($log->action),
                            $log->model_type,
                            $log->model_id ?? '-',
                            $log->description ?? '-',
                            $log->ip_address ?? '-',
                        ]);
                    }
                });

            fclose($handle);
        }, 'audit-'.now()->format('Y-m-d_His').'.csv', $headers);
    }
}
