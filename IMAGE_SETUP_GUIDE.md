# 📸 Guide Complet - Ajouter les Images 4K aux Produits

## Statut Actuel
✅ Base de données peuplée avec 8 produits réalistes  
✅ Descriptions, prix, stock configurés  
⏳ **Images 4K à ajouter**

## Structures de Fichiers de Images

Chaque produit attend les images dans son dossier respectif:

```
public/uploads/products/
└── produit-a/
    ├── a-1.jpg (1920x1440 minimum)
    ├── a-2.jpg
    └── a-3.jpg
```

## Option 1: Télécharger depuis Unsplash (Gratuit - Recommandé)

### Étapes:
1. Allez sur https://unsplash.com
2. Recherchez: **"furniture 4K"** ou **"wardrobe closet"**
3. Cliquez sur une image
4. Cliquez sur "Download" → "Download Free"
5. Renommez l'image selon le produit
6. Placez-la dans `public/uploads/products/{produit-x}/`

### Mots-clés par produit:
- **produit-a** (Commode): "wooden dresser", "chest of drawers 4K"
- **produit-b** (Étagère): "white shelf", "shelving unit 4K"
- **produit-c** (Commode Basse): "gray dresser", "long dresser 4K"
- **produit-d** (Colonne): "narrow storage", "tall cabinet"
- **produit-e** (Dressing): "walk-in wardrobe", "walk-in closet 4K"
- **produit-f** (Armoire): "white wardrobe", "wardrobe cabinet 4K"
- **produit-g** (Placard Coulissant): "sliding door wardrobe", "modern closet"
- **produit-h** (Grand Dressing): "luxury walk-in closet", "designer wardrobe 4K"

## Option 2: Télécharger depuis Pexels (Gratuit)

1. Allez sur https://www.pexels.com
2. Cherchez les termes ci-dessus
3. Téléchargez les images haute résolution
4. Placez dans les dossiers correspondants

## Option 3: Utiliser Pixabay (Gratuit + Premium)

1. Allez sur https://pixabay.com
2. Cherchez "furniture", "wardrobe", "dressing room"
3. Filtrez par "4K" dans les options
4. Téléchargez les images

## Spécifications Techniques

### Résolution Recommandée:
- **4K**: 3840 × 2880 px (idéal)
- **Full HD**: 1920 × 1440 px (minimum)
- **High Res**: 2560 × 1920 px (bon compromis)

### Formats Acceptés:
- JPG/JPEG (recommandé pour web)
- PNG (avec transparence)
- WebP (format moderne optimisé)

### Optimisation Taille:
```bash
# Convertir et compresser avec ImageMagick
convert produit-a-1.jpg -resize 1920x1440 -quality 85 a-1.jpg

# Ou avec ffmpeg
ffmpeg -i produit-a-1.jpg -vf scale=1920:1440 a-1.jpg
```

## Nommage des Fichiers

Respectez exactement les noms définis dans le seeder:

```
produit-a: commode-a-1.jpg, commode-a-2.jpg, commode-a-3.jpg
produit-b: etagere-b-1.jpg, etagere-b-2.jpg, etagere-b-3.jpg
produit-c: commode-c-1.jpg, commode-c-2.jpg, commode-c-3.jpg
produit-d: colonne-d-1.jpg, colonne-d-2.jpg, colonne-d-3.jpg
produit-e: dressing-e-1.jpg, dressing-e-2.jpg, dressing-e-3.jpg, dressing-e-4.jpg
produit-f: armoire-f-1.jpg, armoire-f-2.jpg, armoire-f-3.jpg
produit-g: placard-g-1.jpg, placard-g-2.jpg, placard-g-3.jpg
produit-h: dressing-h-1.jpg, dressing-h-2.jpg, dressing-h-3.jpg, dressing-h-4.jpg
```

## Étapes Installation Complètes

### 1. Télécharger les Images
- Allez sur Unsplash/Pexels
- Cherchez "furniture 4K"
- Téléchargez 3-4 images par produit

### 2. Renommer les Fichiers
- Utilisez les noms listés ci-dessus
- Correspondre exactement aux noms du seeder

### 3. Placer dans les Dossiers
```bash
# Exemple Windows
copy commode-a-1.jpg C:\Users\TSU\Desktop\dressingue\public\uploads\products\produit-a\

# Exemple Linux/Mac
cp commode-a-1.jpg /path/to/project/public/uploads/products/produit-a/
```

### 4. Vérifier l'Affichage
```bash
# Naviguer ver le site
http://localhost:8000/catalog

# Vérifier que les images s'affichent correctement
```

## Script Automatique (Optionnel)

Si vous avez PHP avec GD library activé, vous pouvez générer des placeholders:

```bash
php scripts/generate-product-placeholders.php
```

Cela créera des images de placeholder que vous pouvez remplacer plus tard.

## Vérification

Après avoir ajouté les images, vérifiez:

```bash
# Lister les fichiers ajoutés
dir public\uploads\products\produit-a\

# Vous devriez voir:
commode-a-1.jpg
commode-a-2.jpg
commode-a-3.jpg
```

## Troubleshooting

### Les images ne s'affichent pas?
1. Vérifiez les permissions: `chmod 755 public/uploads/products/`
2. Vérifiez les noms de fichiers (doivent correspondre exactement)
3. Vérifiez que le chemin est correct dans le code

### Erreur de permission?
```bash
# Corriger les permissions
chmod -R 755 public/uploads/products/
```

### Images trop grosses?
Compressez-les avec:
- ImageMagick: `convert input.jpg -quality 85 output.jpg`
- TinyJPG: https://tinyjpg.com/

## Support

Consultez [RAPPORT_DONNEES_REELLES.md](../RAPPORT_DONNEES_REELLES.md) pour plus de détails sur la structure des produits.
