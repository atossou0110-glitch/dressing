<?php

namespace App\Support\Payments;

use App\Models\ProductOrder;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FedapayGateway
{
    /**
     * Determine whether the gateway is ready to use.
     */
    public function isConfigured(): bool
    {
        return filled($this->secretKey());
    }

    /**
     * Create a transaction on FedaPay.
     *
     * @return array<string, mixed>
     */
    public function createTransaction(ProductOrder $order): array
    {
        $this->ensureConfigured();

        $payload = [
            'description' => sprintf('Commande %s - %s', $order->reference, $order->product->name),
            'amount' => (int) $order->amount,
            'reference' => $order->reference,
            'callback_url' => route('payments.fedapay.return', $order),
            'currency' => [
                'iso' => $order->currency,
            ],
            'customer' => [
                'firstname' => $order->customer_first_name,
                'lastname' => $order->customer_last_name,
                'email' => $order->customer_email,
                'phone_number' => [
                    'number' => $this->normalizePhoneNumber($order->customer_phone),
                    'country' => strtolower($order->customer_country),
                ],
            ],
            'metadata' => [
                'order_reference' => $order->reference,
                'payment_method' => $order->payment_method,
                'product_slug' => $order->product->slug,
            ],
        ];

        return $this->unwrapTransactionPayload(
            $this->request()
                ->post('/transactions', $payload)
                ->throw()
                ->json(),
        );
    }

    /**
     * Generate a payment token for a transaction.
     *
     * @return array<string, mixed>
     */
    public function generateToken(string $transactionId): array
    {
        $this->ensureConfigured();

        return $this->unwrapTokenPayload(
            $this->request()
                ->post('/transactions/'.$transactionId.'/token')
                ->throw()
                ->json(),
        );
    }

    /**
     * Trigger a direct mobile money payment.
     *
     * @return array<string, mixed>
     */
    public function sendDirectPayment(string $mode, string $token): array
    {
        $this->ensureConfigured();

        if ($mode === '') {
            throw new RuntimeException('Aucun mode direct FedaPay n a ete fourni.');
        }

        return $this->request()
            ->post('/'.$mode, [
                'token' => $token,
            ])
            ->throw()
            ->json();
    }

    /**
     * Retrieve the latest details of a transaction.
     *
     * @return array<string, mixed>
     */
    public function fetchTransaction(string $transactionId): array
    {
        $this->ensureConfigured();

        return $this->unwrapTransactionPayload(
            $this->request()
                ->get('/transactions/'.$transactionId)
                ->throw()
                ->json(),
        );
    }

    /**
     * Normalize a Beninese phone number for the provider.
     */
    public function normalizePhoneNumber(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return $value;
        }

        if (str_starts_with($digits, '229')) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+229'.substr($digits, 1);
        }

        if (strlen($digits) === 8) {
            return '+229'.$digits;
        }

        return '+'.$digits;
    }

    /**
     * Prepare a signed HTTP client.
     */
    private function request(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->withToken($this->secretKey())
            ->acceptJson()
            ->asJson()
            ->timeout(25);
    }

    /**
     * Unwrap the transaction object from the provider payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function unwrapTransactionPayload(array $payload): array
    {
        foreach (['v1/transaction', 'transaction', 'data'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
        }

        return $payload;
    }

    /**
     * Unwrap the token payload from the provider response.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function unwrapTokenPayload(array $payload): array
    {
        if (isset($payload['token']) || isset($payload['url'])) {
            return $payload;
        }

        foreach ($payload as $value) {
            if (is_array($value) && (isset($value['token']) || isset($value['url']))) {
                return $value;
            }
        }

        return $payload;
    }

    /**
     * Resolve the gateway base URL.
     */
    private function baseUrl(): string
    {
        $configured = (string) config('services.fedapay.base_url');

        if ($configured !== '') {
            return rtrim($configured, '/');
        }

        return $this->environment() === 'live'
            ? 'https://api.fedapay.com/v1'
            : 'https://sandbox-api.fedapay.com/v1';
    }

    /**
     * Resolve the configured environment.
     */
    private function environment(): string
    {
        return (string) config('services.fedapay.environment', 'sandbox');
    }

    /**
     * Resolve the provider secret key.
     */
    private function secretKey(): string
    {
        return (string) config('services.fedapay.secret_key');
    }

    /**
     * Guard against missing configuration.
     */
    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Les cles FedaPay ne sont pas configurees.');
        }
    }
}
