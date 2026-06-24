# King Rangement Benin

Application Laravel 12 pour catalogue, votes, precommandes WhatsApp, avis clients et gestion admin.

## Prerequis

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL

## Installation locale

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

## Commandes utiles

```bash
php artisan test
php artisan catalog:db-overview
php artisan catalog:preflight
php artisan catalog:preflight --production
php artisan catalog:ops:backup-db
php artisan catalog:ops:maintenance on
php artisan catalog:ops:maintenance off
php artisan catalog:ops:release-check --with-tests
```

## Preparation pre-deploiement (sans mise en ligne)

1. Copier le template production:
   `cp .env.production.example .env`
2. Completer les vraies valeurs (`APP_URL`, DB, SMTP, `WHATSAPP_NUMBER`, `FEDAPAY_SECRET_KEY`, etc.).
3. Generer la cle:
   `php artisan key:generate`
4. Executer les migrations:
   `php artisan migrate --force`
5. Construire les assets:
   `npm ci && npm run build`
6. Lancer les verifications:
   `php artisan test`
   `php artisan catalog:preflight --production`

Si `catalog:preflight --production` retourne un code 0 et que les tests passent, la base et l'application sont pretes pour la phase de deploiement.

## Services externes requis

- Base de donnees: `sqlite` pour un lancement local rapide ou `mysql` pour la production.
- WhatsApp: `WHATSAPP_NUMBER` pour activer les precommandes.
- FedaPay: `FEDAPAY_SECRET_KEY` et les autres variables `FEDAPAY_*` pour activer le checkout.
- SMTP: `MAIL_*` pour les emails reels (verification de compte, notifications).

## Exploitation (sans deploiement automatique)

- Backup snapshot DB: `php artisan catalog:ops:backup-db`
- Maintenance ON (page dediee): `php artisan catalog:ops:maintenance on`
- Maintenance OFF: `php artisan catalog:ops:maintenance off`
- Check global pre-release en une commande: `php artisan catalog:ops:release-check --with-tests`
- Variante composer: `composer ops-release-check`

## Notes fonctionnelles

- Les votes et precommandes sont persistants en base (`product_votes`, `product_preorders`) par visiteur.
- Une protection anti-abus de vote est active (limite de tentatives + cooldown reseau configurable).
- Le compte a rebours de vote est persistant et editable depuis le dashboard admin.
- La precommande est bloquee si le vote est ferme et que le visiteur n'a pas deja vote.
