<?php

return [
    'vote_guard' => [
        'enabled' => env('VOTE_GUARD_ENABLED', true),
        'max_attempts_per_hour' => env('VOTE_GUARD_MAX_ATTEMPTS_PER_HOUR', 20),
        'ip_cooldown_hours' => env('VOTE_GUARD_IP_COOLDOWN_HOURS', 24),
    ],

    'products' => [
        'produit-a' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_A_DIR', 'catalog-media/storefront/produit-a'),
            'fallback' => [
                '/assets/commode-etagere/commod.png',
                '/assets/commode-etagere/COMMODE (2).jfif',
                '/assets/commode-etagere/COMMODE (3).jfif',
            ],
        ],
        'produit-b' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_B_DIR', 'catalog-media/storefront/produit-b'),
            'fallback' => [
                '/assets/commode-etagere/etagere.png',
                '/assets/commode-etagere/etagere.jfif',
                '/assets/commode-etagere/etagere (2).jfif',
            ],
        ],
        'produit-c' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_C_DIR', 'catalog-media/storefront/produit-c'),
            'fallback' => [
                '/assets/commode-etagere/COMMODE.jfif',
                '/assets/commode-etagere/COMMODE (4).jfif',
                '/assets/commode-etagere/commod.png',
            ],
        ],
        'produit-d' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_D_DIR', 'catalog-media/storefront/produit-d'),
            'fallback' => [
                '/assets/commode-etagere/ETAGERE (3).jfif',
                '/assets/commode-etagere/etagere.png',
                '/assets/commode-etagere/etagere.jfif',
            ],
        ],
        'produit-e' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_E_DIR', 'catalog-media/storefront/produit-e'),
            'fallback' => [
                '/assets/dressing/dressing.PNG',
                '/assets/dressing/dressing (2).PNG',
                '/assets/dressing/12 idées géniales pour avoir un dressing bien….jfif',
            ],
        ],
        'produit-f' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_F_DIR', 'catalog-media/storefront/produit-f'),
            'fallback' => [
                '/assets/dressing/Installer un dressing chez soi _ tous les bons….jfif',
                '/assets/dressing/Dressing _ comment bien le choisir.jfif',
                '/assets/dressing/dressing (2).PNG',
            ],
        ],
        'produit-g' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_G_DIR', 'catalog-media/storefront/produit-g'),
            'fallback' => [
                '/assets/dressing/12 idées géniales pour avoir un dressing bien….jfif',
                '/assets/dressing/Installer un dressing chez soi _ tous les bons….jfif',
                '/assets/dressing/Dressing _ comment bien le choisir.jfif',
            ],
        ],
        'produit-h' => [
            'directory' => env('CATALOG_MEDIA_PRODUCT_H_DIR', 'catalog-media/storefront/produit-h'),
            'fallback' => [
                '/assets/dressing/dressing.PNG',
                '/assets/dressing/dressing (2).PNG',
                '/assets/dressing/Installer un dressing chez soi _ tous les bons….jfif',
            ],
        ],
    ],
];
