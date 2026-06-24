<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$products = \App\Models\Product::all(['slug', 'name', 'price', 'stock_quantity', 'bestseller', 'featured']);

echo "========================================\n";
echo "✅ PRODUITS CRÉÉS AVEC SUCCÈS\n";
echo "========================================\n\n";

foreach ($products as $product) {
    $badge = '';
    if ($product->bestseller) $badge .= '[BESTSELLER] ';
    if ($product->featured) $badge .= '[FEATURED] ';
    
    echo "📦 {$product->slug}\n";
    echo "   Nom: {$product->name}\n";
    echo "   Prix: " . number_format($product->price, 0, ',', '.') . " FCFA\n";
    echo "   Stock: {$product->stock_quantity} unités\n";
    if ($badge) echo "   {$badge}\n";
    echo "\n";
}

echo "========================================\n";
echo "Total: " . $products->count() . " produits\n";
echo "========================================\n";
?>
