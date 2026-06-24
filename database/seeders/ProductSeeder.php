<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the products table with real data.
     */
    public function run(): void
    {
        $products = [
            [
                'slug' => 'produit-a',
                'code' => 'A',
                'name' => 'Commode 3 Tiroirs Chene Naturel',
                'category' => 'commode',
                'description' => 'Commode basse avec 3 tiroirs spacieux en chene massif. Dimensions: 100cm x 45cm x 75cm. Plateau solide pour accueillir miroir, lampe ou decoration.',
                'short_description' => 'Commode 3 tiroirs en chene naturel',
                'price' => 450000,
                'discount_price' => 405000,
                'stock_quantity' => 12,
                'sku' => 'COMMODE-CHENE-3T',
                'weight' => 45,
                'bestseller' => true,
                'featured' => true,
                'images' => json_encode(['commode-a-1.jpg', 'commode-a-2.jpg', 'commode-a-3.jpg']),
            ],
            [
                'slug' => 'produit-b',
                'code' => 'B',
                'name' => 'Etagere Murale 5 Niveaux Blanc Laque',
                'category' => 'etagere',
                'description' => 'Etagere verticale avec 5 niveaux de rangement. Dimensions: 60cm x 25cm x 200cm. Blanc laque haute brillance.',
                'short_description' => 'Etagere 5 niveaux blanc laque',
                'price' => 320000,
                'discount_price' => 288000,
                'stock_quantity' => 18,
                'sku' => 'ETAGERE-BLANC-5N',
                'weight' => 25,
                'bestseller' => true,
                'featured' => false,
                'images' => json_encode(['etagere-b-1.jpg', 'etagere-b-2.jpg', 'etagere-b-3.jpg']),
            ],
            [
                'slug' => 'produit-c',
                'code' => 'C',
                'name' => 'Commode Basse Gris Souris 4 Tiroirs',
                'category' => 'commode',
                'description' => 'Commode basse avec format horizontal ideal pour sous miroir ou televiseur. 4 tiroirs genereux en gris souris.',
                'short_description' => 'Commode basse gris souris 4 tiroirs',
                'price' => 580000,
                'discount_price' => 522000,
                'stock_quantity' => 8,
                'sku' => 'COMMODE-GRIS-4T',
                'weight' => 55,
                'bestseller' => false,
                'featured' => true,
                'images' => json_encode(['commode-c-1.jpg', 'commode-c-2.jpg', 'commode-c-3.jpg']),
            ],
            [
                'slug' => 'produit-d',
                'code' => 'D',
                'name' => 'Colonne Rangement Etroit Noyer Fonce',
                'category' => 'etagere',
                'description' => 'Colonne verticale compacte ideale pour espaces restreints. 4 etageres ouvertes en noyer fonce.',
                'short_description' => 'Colonne rangement noyer fonce',
                'price' => 280000,
                'discount_price' => 252000,
                'stock_quantity' => 15,
                'sku' => 'COLONNE-NOYER-4N',
                'weight' => 20,
                'bestseller' => true,
                'featured' => false,
                'images' => json_encode(['colonne-d-1.jpg', 'colonne-d-2.jpg', 'colonne-d-3.jpg']),
            ],
            [
                'slug' => 'produit-e',
                'code' => 'E',
                'name' => 'Dressing Ouvert Modulable Chene 2m',
                'category' => 'dressing',
                'description' => 'Dressing ouvert de 2 metres avec composition modulable. Combinaison de penderie, etageres et tiroirs.',
                'short_description' => 'Dressing ouvert 2m modulable',
                'price' => 1200000,
                'discount_price' => 1020000,
                'stock_quantity' => 4,
                'sku' => 'DRESSING-CHENE-2M',
                'weight' => 120,
                'bestseller' => true,
                'featured' => true,
                'images' => json_encode(['dressing-e-1.jpg', 'dressing-e-2.jpg', 'dressing-e-3.jpg', 'dressing-e-4.jpg']),
            ],
            [
                'slug' => 'produit-f',
                'code' => 'F',
                'name' => 'Armoire Fermee 2 Portes Blanc Brillant',
                'category' => 'armoire',
                'description' => 'Armoire fermee avec 2 portes battantes en blanc brillant. Dimensions: 120cm x 60cm x 200cm.',
                'short_description' => 'Armoire fermee 2 portes blanc brillant',
                'price' => 850000,
                'discount_price' => 722500,
                'stock_quantity' => 6,
                'sku' => 'ARMOIRE-BLANC-2P',
                'weight' => 95,
                'bestseller' => false,
                'featured' => true,
                'images' => json_encode(['armoire-f-1.jpg', 'armoire-f-2.jpg', 'armoire-f-3.jpg']),
            ],
            [
                'slug' => 'produit-h',
                'code' => 'H',
                'name' => 'Grand Dressing Premium 2,5m Noyer',
                'category' => 'dressing',
                'description' => 'Grand dressing premium de 2,5 metres en noyer fonce avec penderie, etageres a hauteur variable et tiroirs profonds.',
                'short_description' => 'Grand dressing premium 2,5m noyer',
                'price' => 1800000,
                'discount_price' => 1440000,
                'stock_quantity' => 3,
                'sku' => 'DRESSING-PREMIUM-2.5M',
                'weight' => 180,
                'bestseller' => true,
                'featured' => true,
                'images' => json_encode(['dressing-h-1.jpg', 'dressing-h-2.jpg', 'dressing-h-3.jpg', 'dressing-h-4.jpg']),
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
        }
    }
}
