<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOrder extends Model
{
    use HasFactory;

    /**
     * The only payment method currently exposed to checkout customers.
     */
    public const DEFAULT_CHECKOUT_PAYMENT_METHOD = 'fedapay';

    /**
     * Available payment methods and provider metadata.
     *
     * @var array<string, array<string, string>>
     */
    public const PAYMENT_METHODS = [
        'fedapay' => [
            'label' => 'FedaPay',
            'provider_label' => 'FedaPay',
            'fedapay_mode' => '',
            'checkout_mode' => 'redirect',
        ],
    ];

    /**
     * Friendly labels for transaction states.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        'pending' => 'En attente',
        'approved' => 'Paiement confirme',
        'declined' => 'Paiement refuse',
        'canceled' => 'Paiement annule',
        'transferred' => 'Paiement transfere',
        'refunded' => 'Paiement rembourse',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'reference',
        'provider',
        'payment_method',
        'status',
        'provider_transaction_id',
        'provider_reference',
        'provider_payment_method',
        'provider_status',
        'amount',
        'currency',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_phone',
        'customer_city',
        'customer_country',
        'customer_address',
        'customer_zip_code',
        'notes',
        'provider_payload',
        'payment_initiated_at',
        'paid_at',
        'canceled_at',
        'declined_at',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'provider_payload' => 'array',
        'payment_initiated_at' => 'datetime',
        'paid_at' => 'datetime',
        'canceled_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    /**
     * Resolve route binding by order reference.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /**
     * Product linked to the order.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Payment method metadata for this order.
     *
     * @return array<string, string>
     */
    public function paymentMethodMeta(): array
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? [
            'label' => ucfirst(str_replace('_', ' ', $this->payment_method)),
            'provider_label' => 'Paiement',
            'fedapay_mode' => '',
            'checkout_mode' => 'redirect',
        ];
    }

    /**
     * Human readable payment method.
     */
    public function paymentMethodLabel(): string
    {
        return $this->paymentMethodMeta()['label'];
    }

    /**
     * Payment methods currently available in the public checkout flow.
     *
     * @return array<string, array<string, string>>
     */
    public static function checkoutPaymentMethods(): array
    {
        return array_intersect_key(
            self::PAYMENT_METHODS,
            array_flip([self::DEFAULT_CHECKOUT_PAYMENT_METHOD]),
        );
    }

    /**
     * Default payment method currently enforced in checkout.
     */
    public static function defaultCheckoutPaymentMethod(): string
    {
        return self::DEFAULT_CHECKOUT_PAYMENT_METHOD;
    }

    /**
     * Human readable status.
     */
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Tone used for status pills.
     */
    public function statusTone(): string
    {
        return match ($this->status) {
            'approved', 'transferred' => 'success',
            'declined', 'canceled', 'refunded' => 'danger',
            default => 'warning',
        };
    }

    /**
     * Determine whether the order is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->status, ['approved', 'transferred'], true);
    }

    /**
     * Format the FCFA amount for display.
     */
    public function formattedAmount(): string
    {
        $currency = strtoupper($this->currency) === 'XOF' ? 'FCFA' : $this->currency;

        return number_format((int) $this->amount, 0, ',', ' ').' '.$currency;
    }
}
