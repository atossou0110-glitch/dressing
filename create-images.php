<?php
/**
 * Create minimal JPEG placeholder images
 * Creates valid JPEG files that can be displayed by browsers
 * Usage: php create-images.php
 */

function createPlaceholderJpeg($filepath) {
    // Ensure directory exists
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Create a valid JPEG using the simplest possible structure
    // This is a 1x1 pixel valid JPEG that browsers can display
    $jpeg_data = base64_decode(
        '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8VAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA4AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//Z'
    );
    
    return file_put_contents($filepath, $jpeg_data) !== false;
}

// Product configuration matching database seeder
$products = [
    'produit-a' => ['commode-a-1', 'commode-a-2', 'commode-a-3'],
    'produit-b' => ['etagere-b-1', 'etagere-b-2', 'etagere-b-3'],
    'produit-c' => ['commode-c-1', 'commode-c-2', 'commode-c-3'],
    'produit-d' => ['colonne-d-1', 'colonne-d-2', 'colonne-d-3'],
    'produit-e' => ['dressing-e-1', 'dressing-e-2', 'dressing-e-3', 'dressing-e-4'],
    'produit-f' => ['armoire-f-1', 'armoire-f-2', 'armoire-f-3'],
    'produit-g' => ['placard-g-1', 'placard-g-2', 'placard-g-3'],
    'produit-h' => ['dressing-h-1', 'dressing-h-2', 'dressing-h-3', 'dressing-h-4'],
];

echo "🖼️  Creating JPEG placeholder images..." . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;

$total = 0;
$created = 0;

foreach ($products as $slug => $images) {
    echo PHP_EOL . "📦 $slug" . PHP_EOL;
    
    foreach ($images as $imageName) {
        $filepath = 'public/uploads/products/' . $slug . '/' . $imageName . '.jpg';
        $total++;
        
        if (createPlaceholderJpeg($filepath)) {
            echo "   ✓ " . $imageName . ".jpg" . PHP_EOL;
            $created++;
        } else {
            echo "   ✗ Failed: " . $imageName . ".jpg" . PHP_EOL;
        }
    }
}

echo PHP_EOL . str_repeat("=", 60) . PHP_EOL;
echo "✅ Created $created/$total product images" . PHP_EOL;
?>
