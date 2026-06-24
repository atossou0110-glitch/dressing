# King Rangement Benin - E-Commerce Site

## Project Overview

King Rangement Benin est un site e-commerce complet fusionnant un template Bootstrap premium avec une application Laravel moderne pour une expÃ©rience de vente de meubles premium.

## ðŸŽ¯ Features

### âœ… Pages Principales
- **Accueil** (`/`) - Showcase avec produits recommandÃ©s
- **Catalogue** (`/shop`) - Affichage de tous les produits avec filtres
- **DÃ©tails Produit** (`/product/{slug}`) - Vue dÃ©taillÃ©e avec caractÃ©ristiques
- **Ã€ Propos** (`/about`) - PrÃ©sentation de King Rangement Benin
- **Blog** (`/blog`) - Articles et conseils meubles
- **Contact** (`/contact`) - Formulaire de contact
- **Panier** (`/cart`) - Gestion du panier (localStorage)
- **Commande** (`/checkout`) - Processus d'achat

### ðŸŽ¨ Design
- Template responsive Bootstrap 5
- Branding King Rangement Benin (couleurs: vert #1b6b4d, or #d4a574)
- Logo KW SVG (favicon + variantes)
- Navigation mobile-friendly
- Design premium et professionnel

### ðŸ’¾ Architecture

#### Routes (`routes/web.php`)
```
GET  /                      â†’ home
GET  /shop                  â†’ shop
GET  /product/{slug}        â†’ product detail
GET  /about                 â†’ about page
GET  /blog                  â†’ blog
GET  /contact               â†’ contact form
POST /contact               â†’ contact submission
GET  /cart                  â†’ shopping cart
GET  /checkout              â†’ checkout page
POST /checkout              â†’ process order
```

#### ContrÃ´leurs
- **`ShopController`** - GÃ¨re toutes les pages e-commerce

#### ModÃ¨les
- **`Product`** - Produits avec contenu JSON et relations
- **`ProductReview`** - Avis clients
- **`ProductVote`** - Votes produits
- **`ProductPreorder`** - PrÃ©commandes

#### Vues Blade
```
resources/views/
â”œâ”€â”€ layouts/
â”‚   â””â”€â”€ app.blade.php           # Layout principal
â”œâ”€â”€ shop/
â”‚   â”œâ”€â”€ index.blade.php         # Page d'accueil
â”‚   â””â”€â”€ shop.blade.php          # Catalogue
â”œâ”€â”€ product/
â”‚   â””â”€â”€ show.blade.php          # DÃ©tail produit
â””â”€â”€ pages/
    â”œâ”€â”€ about.blade.php         # Ã€ propos
    â”œâ”€â”€ blog.blade.php          # Blog
    â”œâ”€â”€ contact.blade.php       # Contact
    â”œâ”€â”€ cart.blade.php          # Panier
    â””â”€â”€ checkout.blade.php      # Commande
```

### ðŸ“¦ Assets

#### Fichiers CSS
- Bootstrap 5
- Font Awesome
- Elegant Icons
- Magnific Popup
- Nice Select
- Owl Carousel
- Slick Nav
- Style personnalisÃ©

#### Images
- Heroes (banniÃ¨res)
- Produits (14 images)
- About (team, testimonials)
- Blog (9 articles)
- Icons, banners, gallerie

#### Fonts
- Google: Nunito Sans (300-900)
- Font Awesome
- Elegant Icons

#### Logos
- `public/kw-favicon.svg` - Favicon (100x100)
- `public/kw-logo.svg` - Logo circulaire complet
- `public/kw-logo-preview.html` - AperÃ§u des logos

## ðŸš€ Installation & Setup

### PrÃ©requis
- PHP 8.1+
- Composer
- Node.js (optionnel)
- Laravel 11

### Installation

1. **Clone/Setup du projet**
```bash
cd c:\Users\TSU\Desktop\dressingue
composer install
```

2. **Environnement**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Base de donnÃ©es**
```bash
php artisan migrate
php artisan db:seed
```

4. **DÃ©marrage**
```bash
php artisan serve
# AccÃ©dez Ã  http://localhost:8000
```

## ðŸ“± Panier & Checkout

Le panier utilise **localStorage** pour une gestion cÃ´tÃ© client:
- Ajout produits : Bouton "Ajouter au panier"
- Visualisation : Page `/cart`
- Commande : Page `/checkout`
- DonnÃ©es sauvegardÃ©es dans le navigateur

## ðŸ›’ Produits

Deux produits de base sont seeded dans la BD:

1. **Produit A (commode)** - EUR 899
2. **Produit B (meuble tiroirs)** - EUR 499

### Structure Produit
```php
{
    "slug": "produit-a",
    "code": "A",
    "name": "commode",
    "content": {
        "home_badge": "Nouveau",
        "home_price": "EUR 899",
        "home_description": "...",
        "features": [...],
        "specifications": [...]
    }
}
```

## ðŸŽ¨ Branding

- **Nom**: King Rangement Benin
- **Logo**: KW (Kustom Wardrobes / Kitchens & Workspace)
- **Couleurs**: Vert #1b6b4d, Or #d4a574
- **Slogan**: Meubles de luxe essentiels

## ðŸ”’ Security

- CSRF protection
- Throttling sur routes sensibles
- Validation des formulaires
- Authentication middleware prÃªt

## ðŸ“Š Analytics & Metrics

- Compteur de votes produits
- Compteur de prÃ©commandes
- Gestion des avis clients
- Tracking des interactions

## ðŸ”„ IntÃ©grations Futures

- [ ] SystÃ¨me de paiement (Stripe, PayPal)
- [ ] Email notifications
- [ ] Wishlist utilisateur
- [ ] Comparaison produits
- [ ] SystÃ¨me de recommandations
- [ ] Admin panel complet
- [ ] Gestion des commandes
- [ ] Suivi des expÃ©ditions

## ðŸ“„ Documentation

- **README**: Ce fichier
- **SECURITY_DEPLOYMENT.md**: Guide de dÃ©ploiement sÃ©curisÃ©
- **SECURITY_MAINTENANCE.md**: Maintenance et sÃ©curitÃ©

## ðŸ‘¨â€ðŸ’¼ Support

Pour toute question ou modification, contactez l'Ã©quipe King Rangement Benin.

---

**Fait avec â¤ï¸ pour King Rangement Benin**
E-Commerce Platform 2026
