#!/usr/bin/env php
<?php

/**
 * Script pour télécharger les images des produits depuis Unsplash/Pexels
 * Usage: php scripts/download-product-images.php
 */

$products = [
    'produit-a' => [
        'name' => 'Commode 3 Tiroirs',
        'query' => 'wooden dresser 4k high resolution',
        'images' => 3,
    ],
    'produit-b' => [
        'name' => 'Étagère Murale',
        'query' => 'white shelf 4k modern furniture',
        'images' => 3,
    ],
    'produit-c' => [
        'name' => 'Commode Basse',
        'query' => 'gray dresser furniture 4k',
        'images' => 3,
    ],
    'produit-d' => [
        'name' => 'Colonne Rangement',
        'query' => 'narrow storage cabinet 4k',
        'images' => 3,
    ],
    'produit-e' => [
        'name' => 'Dressing Ouvert',
        'query' => 'walk-in wardrobe closet 4k',
        'images' => 4,
    ],
    'produit-f' => [
        'name' => 'Armoire Fermée',
        'query' => 'white wardrobe cabinet 4k',
        'images' => 3,
    ],
    'produit-g' => [
        'name' => 'Placard Coulissant',
        'query' => 'sliding door wardrobe 4k',
        'images' => 3,
    ],
    'produit-h' => [
        'name' => 'Grand Dressing Premium',
        'query' => 'luxury walk-in closet 4k premium',
        'images' => 4,
    ],
];

echo "🖼️  Script de téléchargement d'images produits\n";
echo "================================================\n\n";

foreach ($products as $slug => $info) {
    $dir = __DIR__ . "/../public/uploads/products/$slug";
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    echo "📦 {$info['name']} ({$slug})\n";
    echo "   Query: {$info['query']}\n";
    echo "   Images à télécharger: {$info['images']}\n";
    echo "   Dossier: $dir\n";
    
    // Note: Pour implémenter le vrai téléchargement, il faudrait :
    // 1. S'inscrire auprès d'Unsplash ou Pexels
    // 2. Utiliser leur API
    // 3. Télécharger les images correspondantes
    
    echo "   ⚠️  Téléchargez manuellement les images ou utilisez une API\n\n";
}

echo "✅ Instructions générées.\n";
echo "\n📚 Ressources:\n";
echo "   - Unsplash API: https://unsplash.com/api\n";
echo "   - Pexels API: https://www.pexels.com/api/\n";
echo "   - Placez les images dans les dossiers listés ci-dessus\n";
?>
