# Guide Images Produits

## Structure créée

Les dossiers suivants ont été créés pour accueillir les images des produits :

```
public/uploads/products/
├── produit-a/      (Commode 3 Tiroirs)
├── produit-b/      (Étagère Murale)
├── produit-c/      (Commode Basse)
├── produit-d/      (Colonne Rangement)
├── produit-e/      (Dressing Ouvert)
├── produit-f/      (Armoire Fermée)
├── produit-g/      (Placard Coulissant)
└── produit-h/      (Grand Dressing Premium)
```

## Ajouter les images

Pour chaque produit, placez les images dans le dossier correspondant :

- **produit-a** : 3 images → `commode-a-1.jpg`, `commode-a-2.jpg`, `commode-a-3.jpg`
- **produit-b** : 3 images → `etagere-b-1.jpg`, `etagere-b-2.jpg`, `etagere-b-3.jpg`
- **produit-c** : 3 images → `commode-c-1.jpg`, `commode-c-2.jpg`, `commode-c-3.jpg`
- **produit-d** : 3 images → `colonne-d-1.jpg`, `colonne-d-2.jpg`, `colonne-d-3.jpg`
- **produit-e** : 4 images → `dressing-e-1.jpg`, `dressing-e-2.jpg`, `dressing-e-3.jpg`, `dressing-e-4.jpg`
- **produit-f** : 3 images → `armoire-f-1.jpg`, `armoire-f-2.jpg`, `armoire-f-3.jpg`
- **produit-g** : 3 images → `placard-g-1.jpg`, `placard-g-2.jpg`, `placard-g-3.jpg`
- **produit-h** : 4 images → `dressing-h-1.jpg`, `dressing-h-2.jpg`, `dressing-h-3.jpg`, `dressing-h-4.jpg`

## Sources recommandées pour images gratuites

- [Unsplash](https://unsplash.com) - Images gratuites haute résolution
- [Pexels](https://www.pexels.com) - Stock d'images libres
- [Pixabay](https://pixabay.com) - Images et vidéos gratuites
- Recherche : "furniture 4K", "dressing closet", "wooden wardrobe"

## Formats acceptés

- JPG/JPEG
- PNG
- WebP (recommandé pour web)

## Résolution recommandée

- Minimum : 1920x1440 (4K requis par l'utilisateur)
- Optimale : 3840x2880 ou supérieur

## Exécuter le seeder

Pour remplir la base de données avec les 8 produits :

```bash
php artisan db:seed
```

Ou spécifiquement :

```bash
php artisan db:seed --class=ProductSeeder
```

Les produits seront créés avec leurs descriptions, prix et stock.
