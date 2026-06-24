<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Support\ProductGalleryManager;
use App\Support\LoyaltyProgramService;
use App\Support\NewsletterAnalyticsService;
use App\Support\Payments\FedapayGateway;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProductCheckoutController extends Controller
{
    public function __construct(
        private readonly FedapayGateway $fedapay,
        private readonly ProductGalleryManager $galleryManager,
        private readonly NewsletterAnalyticsService $newsletterAnalytics,
        private readonly LoyaltyProgramService $loyaltyProgram,
    ) {}

    /**
     * Show the checkout page for a product.
     */
    public function show(Product $product): View
    {
        Product::syncRequiredCatalog();

        $product = Product::query()->findOrFail($product->id);

        abort_unless($product->isStorefrontVisible(), 404);

        return view('checkout', [
            'product' => $product,
            'paymentMethod' => ProductOrder::PAYMENT_METHODS[ProductOrder::defaultCheckoutPaymentMethod()],
            'defaultPaymentMethod' => ProductOrder::defaultCheckoutPaymentMethod(),
            'checkoutAmount' => $product->checkoutAmount(),
            'checkoutCurrency' => $product->checkoutCurrency(),
            'fedapayConfigured' => $this->fedapay->isConfigured(),
            'productImageUrl' => $this->galleryManager->galleryUrls($product)[0] ?? '/images/commod.png',
        ]);
    }

    /**
     * Create an order and initiate payment.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        Product::syncRequiredCatalog();

        $product = Product::query()->findOrFail($product->id);

        abort_unless($product->isStorefrontVisible(), 404);

        $amount = $product->checkoutAmount();

        if ($amount === null) {
            return back()
                ->withInput()
                ->withErrors([
                    'payment' => 'Le prix de ce produit ne permet pas encore un paiement en ligne.',
                ]);
        }

        if (! $this->fedapay->isConfigured()) {
            return back()
                ->withInput()
                ->withErrors([
                    'payment' => 'Configurez les cles FedaPay avant d activer les paiements.',
                ]);
        }

        $validated = $request->validate([
            'customer_first_name' => ['required', 'string', 'max:80'],
            'customer_last_name' => ['required', 'string', 'max:80'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:32'],
            'customer_city' => ['nullable', 'string', 'max:80'],
            'customer_country' => ['nullable', 'string', 'size:2'],
            'customer_address' => ['required', 'string', 'max:255'],
            'customer_zip_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:'.implode(',', array_keys(ProductOrder::checkoutPaymentMethods()))],
        ]);

        $order = ProductOrder::query()->create([
            'product_id' => $product->id,
            'reference' => $this->newOrderReference(),
            'provider' => 'fedapay',
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'amount' => $amount,
            'currency' => $product->checkoutCurrency(),
            'customer_first_name' => $validated['customer_first_name'],
            'customer_last_name' => $validated['customer_last_name'],
            'customer_email' => ($validated['customer_email'] ?? '') ?: null,
            'customer_phone' => $validated['customer_phone'],
            'customer_city' => ($validated['customer_city'] ?? '') ?: 'Cotonou',
            'customer_country' => strtoupper((string) (($validated['customer_country'] ?? '') ?: 'BJ')),
            'customer_address' => $validated['customer_address'],
            'customer_zip_code' => ($validated['customer_zip_code'] ?? '') ?: null,
            'notes' => ($validated['notes'] ?? '') ?: null,
        ]);

        try {
            $order->load('product');

            $transaction = $this->fedapay->createTransaction($order);
            $this->applyProviderTransaction($order, $transaction, [
                'created_transaction' => $transaction,
            ]);

            $token = $this->fedapay->generateToken((string) $order->provider_transaction_id);
            $this->appendProviderPayload($order, [
                'generated_token' => $token,
            ]);

            $meta = $order->paymentMethodMeta();

            if ($meta['checkout_mode'] === 'redirect') {
                $paymentUrl = (string) ($token['url'] ?? '');

                if ($paymentUrl === '') {
                    throw new \RuntimeException('FedaPay n a pas retourne de lien de paiement.');
                }

                return redirect()->away($paymentUrl);
            }

            $directResponse = $this->fedapay->sendDirectPayment($meta['fedapay_mode'], (string) ($token['token'] ?? ''));

            $order->forceFill([
                'payment_initiated_at' => now(),
            ])->save();

            $this->appendProviderPayload($order, [
                'direct_payment' => $directResponse,
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('payment_notice', 'La demande de paiement a ete envoyee. Validez maintenant sur votre telephone.');
        } catch (Throwable $throwable) {
            Log::warning('Checkout initialization failed', [
                'order_reference' => $order->reference,
                'product_slug' => $product->slug,
                'message' => $throwable->getMessage(),
            ]);

            $this->appendProviderPayload($order, [
                'initialization_error' => $throwable->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'payment' => 'Impossible d initialiser ce paiement pour le moment. Reessayez dans quelques instants.',
                ]);
        }
    }

    /**
     * Show the current state of an order.
     */
    public function showOrder(ProductOrder $order): View
    {
        $order->load('product');
        $this->syncOrderFromProvider($order);
        $order->refresh()->load('product');

        return view('checkout-status', [
            'order' => $order,
            'statusUrl' => route('orders.status', $order),
            'shouldPoll' => ! $order->isPaid() && in_array($order->status, ['pending'], true),
        ]);
    }

    /**
     * Return the latest order status as JSON.
     */
    public function status(ProductOrder $order): JsonResponse
    {
        $this->syncOrderFromProvider($order);
        $order->refresh();

        return response()->json([
            'reference' => $order->reference,
            'status' => $order->status,
            'statusLabel' => $order->statusLabel(),
            'statusTone' => $order->statusTone(),
            'isPaid' => $order->isPaid(),
            'paymentMethodLabel' => $order->paymentMethodLabel(),
            'amount' => $order->formattedAmount(),
        ]);
    }

    /**
     * Handle the secure return from FedaPay.
     */
    public function fedapayReturn(Request $request, ProductOrder $order): RedirectResponse
    {
        $transactionId = (string) $request->query('id', '');

        if ($transactionId !== '' && blank($order->provider_transaction_id)) {
            $order->forceFill([
                'provider_transaction_id' => $transactionId,
            ])->save();
        }

        $this->syncOrderFromProvider($order);

        return redirect()->route('orders.show', $order);
    }

    /**
     * Receive FedaPay webhook notifications.
     */
    public function fedapayWebhook(Request $request): JsonResponse
    {
        $payload = $request->json()->all();
        $transactionId = $this->extractTransactionId($payload);

        if ($transactionId === null) {
            return response()->json(['received' => true]);
        }

        $order = ProductOrder::query()
            ->where('provider_transaction_id', (string) $transactionId)
            ->orWhere('provider_reference', (string) $transactionId)
            ->first();

        if ($order !== null) {
            try {
                $transaction = $this->fedapay->fetchTransaction((string) $order->provider_transaction_id);
                $this->applyProviderTransaction($order, $transaction, [
                    'webhook_event' => $payload,
                ]);
            } catch (Throwable $throwable) {
                Log::warning('FedaPay webhook sync failed', [
                    'order_reference' => $order->reference,
                    'transaction_id' => $transactionId,
                    'message' => $throwable->getMessage(),
                ]);
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Synchronize one order with the provider.
     */
    private function syncOrderFromProvider(ProductOrder $order): void
    {
        if (! $this->fedapay->isConfigured() || blank($order->provider_transaction_id)) {
            return;
        }

        try {
            $transaction = $this->fedapay->fetchTransaction((string) $order->provider_transaction_id);
            $this->applyProviderTransaction($order, $transaction);
        } catch (Throwable $throwable) {
            Log::warning('Unable to refresh payment status', [
                'order_reference' => $order->reference,
                'transaction_id' => $order->provider_transaction_id,
                'message' => $throwable->getMessage(),
            ]);
        }
    }

    /**
     * Apply a provider transaction payload to an order.
     *
     * @param  array<string, mixed>  $transaction
     * @param  array<string, mixed>  $extraPayload
     */
    private function applyProviderTransaction(ProductOrder $order, array $transaction, array $extraPayload = []): void
    {
        $status = (string) ($transaction['status'] ?? $order->status);
        $wasPaid = $order->isPaid();

        $order->forceFill([
            'provider_transaction_id' => isset($transaction['id']) ? (string) $transaction['id'] : $order->provider_transaction_id,
            'provider_reference' => isset($transaction['reference']) ? (string) $transaction['reference'] : $order->provider_reference,
            'provider_payment_method' => isset($transaction['mode']) ? (string) $transaction['mode'] : $order->provider_payment_method,
            'provider_status' => $status,
            'status' => $this->normalizeStatus($status),
            'paid_at' => $this->dateOrExisting($transaction['approved_at'] ?? null, $order->paid_at),
            'canceled_at' => $this->dateOrExisting($transaction['canceled_at'] ?? null, $order->canceled_at),
            'declined_at' => $this->dateOrExisting($transaction['declined_at'] ?? null, $order->declined_at),
        ])->save();

        $this->appendProviderPayload($order, array_merge([
            'last_transaction' => $transaction,
        ], $extraPayload));

        if (! $wasPaid && $order->isPaid()) {
            $this->newsletterAnalytics->trackApprovedOrder($order);
            $order->refresh();
            $this->loyaltyProgram->rewardApprovedOrder($order);
        }
    }

    /**
     * Merge provider payload fragments on the order.
     *
     * @param  array<string, mixed>  $fragment
     */
    private function appendProviderPayload(ProductOrder $order, array $fragment): void
    {
        $payload = is_array($order->provider_payload) ? $order->provider_payload : [];

        foreach ($fragment as $key => $value) {
            $payload[$key] = $value;
        }

        $order->forceFill([
            'provider_payload' => $payload,
        ])->save();
    }

    /**
     * Extract a transaction id from a webhook event payload.
     *
     * @param  array<string, mixed>  $payload
     */
    private function extractTransactionId(array $payload): ?string
    {
        foreach ([
            data_get($payload, 'entity.id'),
            data_get($payload, 'data.id'),
            data_get($payload, 'transaction.id'),
            data_get($payload, 'v1/transaction.id'),
        ] as $candidate) {
            if ($candidate !== null && $candidate !== '') {
                return (string) $candidate;
            }
        }

        return null;
    }

    /**
     * Generate a public order reference.
     */
    private function newOrderReference(): string
    {
        do {
            $reference = 'DD-'.Str::upper(Str::random(10));
        } while (ProductOrder::query()->where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Normalize a provider status.
     */
    private function normalizeStatus(string $status): string
    {
        return in_array($status, array_keys(ProductOrder::STATUS_LABELS), true)
            ? $status
            : 'pending';
    }

    /**
     * Resolve a provider timestamp while keeping existing values.
     */
    private function dateOrExisting(mixed $value, mixed $existing): mixed
    {
        if ($value === null || $value === '') {
            return $existing;
        }

        return Carbon::parse((string) $value);
    }
}
