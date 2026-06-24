<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActions
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->is_admin) {
            return $next($request);
        }

        $response = $next($request);

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $action = match ($request->getMethod()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'unknown',
            };

            $modelType = $this->extractModelType($request);
            $modelId = $this->extractModelId($request);
            $description = $this->buildDescription($request, $action);

            AuditLog::log(
                action: $action,
                modelType: $modelType,
                modelId: $modelId,
                description: $description,
            );
        }

        return $response;
    }

    private function extractModelType(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? '';

        return match (true) {
            str_contains($routeName, 'admin.products') => 'Product',
            str_contains($routeName, 'admin.reviews') => 'ProductReview',
            str_contains($routeName, 'admin.flash-campaigns') => 'FlashCampaign',
            str_contains($routeName, 'admin.support') => 'SupportConversation',
            str_contains($routeName, 'admin.settings') => 'SiteSetting',
            default => class_basename($request->route()?->controller ?? 'Unknown'),
        };
    }

    private function extractModelId(Request $request): ?int
    {
        $id = $request->route('product')?->id
            ?? $request->route('review')?->id
            ?? $request->route('supportConversation')?->id
            ?? null;

        return $id === null ? null : (int) $id;
    }

    private function buildDescription(Request $request, string $action): string
    {
        $routeName = $request->route()?->getName() ?? 'unknown';

        return match (true) {
            str_contains($routeName, 'products.store') => 'Nouveau produit créé',
            str_contains($routeName, 'products.update') => 'Produit modifié',
            str_contains($routeName, 'products.reset') => 'Compteurs produit réinitialisés',
            str_contains($routeName, 'images.store') => 'Images produit ajoutées',
            str_contains($routeName, 'images.destroy') => 'Image produit supprimée',
            str_contains($routeName, 'settings.whatsapp') => 'Paramètres WhatsApp mis à jour',
            str_contains($routeName, 'reviews.destroy') => 'Avis client supprimé',
            str_contains($routeName, 'flash-campaigns.store') => 'Campagne flash créée',
            str_contains($routeName, 'support.update') => 'Conversation support mise à jour',
            default => ucfirst($action).' effectué',
        };
    }
}
