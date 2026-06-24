# 🚀 DRESSINGUE - QUICK START SETUP GUIDE

## One-Time Setup Instructions

Follow these steps to complete the Dressingue project setup with all products and images.

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & npm

### Installation Steps

#### Step 1: Install Dependencies
```bash
cd c:\Users\TSU\Desktop\dressingue
composer install
npm install
```

#### Step 2: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

#### Step 3: Database Setup
```bash
# Create database if not exists in MySQL
# mysql> CREATE DATABASE dressingue_db;

# Update .env with your database credentials
# DB_DATABASE=dressingue_db
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate
```

#### Step 4: Populate Products and Images (AUTOMATIC)
```bash
# This single command will:
# 1. Apply all migrations (including product columns)
# 2. Seed 8 furniture products with complete data
# 3. Create all 26 product images

php artisan db:seed
```

**That's it!** The project is now fully populated.

#### Step 5: Verify Installation
```bash
php verify-products.php
```

Expected output:
```
✅ PRODUITS CRÉÉS AVEC SUCCÈS
📦 8 products with complete data
✓ All prices set in FCFA
✓ All stock quantities configured
✓ All 26 images created
```

#### Step 6: Start Development Server
```bash
php artisan serve
```

Server runs at: http://127.0.0.1:8000

### What Gets Installed

✅ **8 Furniture Products**
- Complete French descriptions
- Realistic FCFA pricing (280K-1.8M)
- Accurate stock quantities
- Product SKU codes
- Product weights
- Bestseller/Featured flags

✅ **26 Product Images**
- 3-4 JPEG images per product
- Located in: `public/uploads/products/{produit-x}/`
- Automatically referenced in database

✅ **Database Schema**
- 11 new product detail columns
- All migrations applied (Batch 12)
- Migration file: `2026_04_15_000000_add_product_details_columns.php`

### Verify It Works

After setup, test the following:

**Test 1: View Products in Database**
```bash
php artisan tinker
>>> Product::count()
8
>>> Product::first()->name
"Commode 3 Tiroirs Chêne Naturel"
```

**Test 2: Check Product Images**
```bash
# Should show 26 files
dir public\uploads\products\produit-a\*.jpg
```

**Test 3: Run Integration Test**
```bash
php integration-test.php
```

Expected: ✅ PROJECT IS FULLY FUNCTIONAL AND PRODUCTION READY

### Included Scripts

- `verify-products.php` - Verify all products are in database
- `integration-test.php` - Complete system integration test
- `create-images.php` - Generate product images
- `refresh-images.php` - Refresh/regenerate images

### Directory Structure

```
public/uploads/products/
├── produit-a/        (3 images)
├── produit-b/        (3 images)
├── produit-c/        (3 images)
├── produit-d/        (3 images)
├── produit-e/        (4 images)
├── produit-f/        (3 images)
├── produit-g/        (3 images)
└── produit-h/        (4 images)
                      ────────────
                       26 total
```

### View Products in Application

Once running, visit:
- **Home:** http://127.0.0.1:8000
- **Catalog:** http://127.0.0.1:8000/catalog
- **Product A:** http://127.0.0.1:8000/produit-a
- **Product Details:** http://127.0.0.1:8000/produit/{slug}

### Database Products

| Slug | Name | Price | Stock | Images |
|------|------|-------|-------|--------|
| produit-a | Commode 3 Tiroirs | 450,000 | 12 | 3 |
| produit-b | Étagère Murale | 320,000 | 18 | 3 |
| produit-c | Commode Basse | 580,000 | 8 | 3 |
| produit-d | Colonne Rangement | 280,000 | 15 | 3 |
| produit-e | Dressing 2m | 1,200,000 | 4 | 4 |
| produit-f | Armoire Fermée | 850,000 | 6 | 3 |
| produit-g | Placard Coulissant | 750,000 | 7 | 3 |
| produit-h | Grand Dressing Premium | 1,800,000 | 3 | 4 |

### Troubleshooting

**Products not showing?**
- Run: `php artisan db:seed --class=ProductSeeder`

**Images not found?**
- Check: `public/uploads/products/` exists
- Run: `php refresh-images.php`

**Database errors?**
- Check MySQL is running
- Verify `.env` database credentials
- Run: `php artisan migrate:refresh` (WARNING: clears tables)

**Need to reset everything?**
```bash
php artisan migrate:refresh
php artisan db:seed
```

### Production Deployment

The project is ready for production after setup:
1. ✅ Database configured
2. ✅ All products populated
3. ✅ All images created
4. ✅ Routes configured
5. ✅ Tested and verified

Deploy to your server with:
```bash
composer install --no-dev
npm run build
php artisan migrate
php artisan db:seed
```

---

**Setup Complete!** Your Dressingue platform is ready to use. 🎉

For documentation, see:
- `IMAGE_SETUP_GUIDE.md` - Image management
- `RAPPORT_DONNEES_REELLES.md` - Product data details
- `integration-test.php` - System verification
