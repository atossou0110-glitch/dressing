<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Product>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sequence = 0;

        $sequence++;
        $codePool = array_merge(range('C', 'Z'), range('0', '9'));
        $code = $codePool[($sequence - 1) % count($codePool)];
        $name = 'Produit test '.$sequence;

        return [
            'slug' => 'produit-test-'.$sequence.'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'code' => $code,
            'category' => $this->faker->randomElement(['commode', 'etagere', 'dressing']),
            'name' => $name,
            'content' => array_replace(Product::baseContentTemplate(), [
                'home_badge' => 'Test',
                'home_description' => 'Produit cree pour les tests automatises.',
                'home_price' => '199 000 FCFA',
                'home_highlight' => 'Rangement utile pour validation.',
                'detail_subtitle' => 'Produit de test pour les exports dashboard.',
                'detail_badge' => 'Produit test',
                'detail_description' => 'Description complete du produit de test.',
                'features' => [
                    'Volume utile',
                    'Structure stable',
                ],
                'specifications' => [
                    'Dimensions : 100 x 80 x 40 cm',
                    'Materiaux : bois',
                ],
            ]),
            'vote_count' => 0,
            'preorder_count' => 0,
        ];
    }
}
