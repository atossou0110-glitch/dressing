#!/usr/bin/env php
<?php

/**
 * Script to generate placeholder images for products
 * These can be replaced with actual 4K images later
 * Usage: php scripts/generate-product-placeholders.php
 */

// Create a simple placeholder image using GD library
function createPlaceholder($productSlug, $productName, $filename) {
    $width = 1920;
    $height = 1440;
    
    // Try to use GD if available
    if (extension_loaded('gd')) {
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $bgColor = imagecolorallocate($image, 240, 240, 240);
        $textColor = imagecolorallocate($image, 80, 80, 80);
        $accentColor = imagecolorallocate($image, 200, 100, 50);
        
        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
        
        // Draw accent bar
        imagefilledrectangle($image, 0, 0, $width, 100, $accentColor);
        
        // Add text
        $fontFile = __DIR__ . '/../storage/fonts/arial.ttf';
        
        // Fallback text
        $text = strtoupper($productSlug);
        imagestring($image, 5, 50, 50, $text, $textColor);
        
        // Save image
        $path = __DIR__ . "/../public/uploads/products/{$productSlug}/{$filename}";
        imagejpeg($image, $path, 90);
        imagedestroy($image);
        
        return true;
    }
    
    return false;
}

echo "🖼️  Placeholder Image Generator\n";
echo "==============================\n\n";

$products = [
    'produit-a' => ['Commode 3 Tiroirs', 3],
    'produit-b' => ['Étagère Murale', 3],
    'produit-c' => ['Commode Basse', 3],
    'produit-d' => ['Colonne Rangement', 3],
    'produit-e' => ['Dressing Ouvert', 4],
    'produit-f' => ['Armoire Fermée', 3],
    'produit-g' => ['Placard Coulissant', 3],
    'produit-h' => ['Grand Dressing Premium', 4],
];

$gdAvailable = extension_loaded('gd');

if (!$gdAvailable) {
    echo "⚠️  GD Library not available. Placeholders cannot be auto-generated.\n\n";
    echo "📥 Download 4K images from these sources:\n\n";
}

foreach ($products as $slug => [$name, $count]) {
    echo "📦 {$slug} ({$name})\n";
    
    if (!is_dir(__DIR__ . "/../public/uploads/products/{$slug}")) {
        mkdir(__DIR__ . "/../public/uploads/products/{$slug}", 0755, true);
    }
    
    for ($i = 1; $i <= $count; $i++) {
        $filename = str_replace('-', '-', substr($slug, 8)) . "-{$i}.jpg";
        if (str_contains($slug, 'produit-')) {
            $filename = strtolower(str_replace('produit-', '', $slug)) . "-{$i}.jpg";
        }
        
        if ($gdAvailable) {
            createPlaceholder($slug, $name, $filename);
            echo "   ✓ Generated: {$filename}\n";
        } else {
            $searchQuery = urlencode($name . " furniture 4K");
            echo "   📷 Image {$i}: Search 'furniture' on Unsplash or download from link\n";
            echo "      Save as: public/uploads/products/{$slug}/{$filename}\n";
        }
    }
    echo "\n";
}

if ($gdAvailable) {
    echo "✅ Placeholder images generated successfully!\n";
} else {
    echo "\n📚 Recommended sources for 4K product images:\n";
    echo "   • Unsplash: https://unsplash.com (search: furniture 4K)\n";
    echo "   • Pexels: https://www.pexels.com (search: furniture)\n";
    echo "   • Pixabay: https://pixabay.com (search: wardrobe furniture)\n";
    echo "   • Shutterstock: https://www.shutterstock.com (premium 4K)\n\n";
    echo "💡 Tip: Download high-resolution images (minimum 1920x1440 or 4K 3840x2880)\n";
}
?>
