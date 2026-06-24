# Deploiement Vercel

Ce projet est une application Laravel. Sur Vercel, le code est deploye depuis GitHub, mais les secrets de production doivent etre ajoutes dans le tableau de bord Vercel.

## Variables obligatoires

Dans Vercel, ouvrir le projet, puis `Settings` > `Environment Variables`, et ajouter :

```text
APP_KEY=base64:CHANGE_ME
APP_URL=https://dressing-habw.vercel.app
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=CHANGE_ME
DB_PORT=3306
DB_DATABASE=CHANGE_ME
DB_USERNAME=CHANGE_ME
DB_PASSWORD=CHANGE_ME
SESSION_DRIVER=cookie
CACHE_STORE=array
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
```

`APP_KEY` doit etre generee avec :

```bash
php artisan key:generate --show
```

Les valeurs `DB_*` doivent correspondre a une base MySQL externe deja creee, par exemple chez AlwaysData, Railway, PlanetScale ou un hebergement cPanel.

## Apres ajout des variables

Relancer un deploiement Vercel depuis l'onglet `Deployments` avec `Redeploy`.
