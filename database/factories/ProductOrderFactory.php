<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductOrder>
 */
class ProductOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ProductOrder>
     */
    protected $model = ProductOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'reference' => 'KR-'.$this->faker->unique()->bothify('########'),
            'provider' => 'fedapay',
            'payment_method' => ProductOrder::DEFAULT_CHECKOUT_PAYMENT_METHOD,
            'status' => 'pending',
            'amount' => 199000,
            'currency' => 'XOF',
            'customer_first_name' => $this->faker->firstName(),
            'customer_last_name' => $this->faker->lastName(),
            'customer_email' => $this->faker->safeEmail(),
            'customer_phone' => '+22997000000',
            'customer_city' => 'Cotonou',
            'customer_country' => 'BJ',
            'customer_address' => 'Adresse de test',
            'customer_zip_code' => null,
            'notes' => null,
            'provider_payload' => [],
            'payment_initiated_at' => null,
            'paid_at' => null,
            'canceled_at' => null,
            'declined_at' => null,
        ];
    }
}
