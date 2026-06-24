# 🔒 Recommandations de Sécurité - Maintien en Continu

## 1. Vérification Régulière des Packages

### Vérifier les vulnerabilités connues
```bash
# Avec Composer
composer audit

# Avec npm (si assets locaux)
npm audit
```

### Mise à jour des dépendances
```bash
composer update --no-dev
npm update
```

---

## 2. Monitorer les Logs

### Surveillance des erreurs
```bash
# Erreurs en production
tail -f storage/logs/laravel.log | grep "ERROR"

# Déterminer les tendances
grep "ERROR.*Admin" storage/logs/laravel.log | wc -l
```

### Alertes sur tentatives de sécurité
```bash
# Les brutes force sur votes
grep "Vous avez deja vote" storage/logs/laravel.log

# Les tentatives d'accès non autorisé
grep "403\|Unauthorized" storage/logs/laravel.log
```

---

## 3. Backups et Disaster Recovery

### Backup Automatisé
```bash
# Script de backup journalier (crontab)
0 2 * * * /usr/bin/mysqldump -u dressingue_user -p$DB_PASSWORD dressingue > /backups/db_$(date +\%Y\%m\%d).sql

# Upload vers cloud (optionnel)
0 3 * * * aws s3 cp /backups/ s3://mon-bucket-backup/ --recursive
```

### Restauration
```bash
mysql -u dressingue_user -p$DB_PASSWORD dressingue < /backups/db_20260410.sql
```

---

## 4. Rotation des Credentials

### Changer les mots de passe régulièrement
```bash
# Mettre à jour DB_PASSWORD tous les 90 jours
# Changer le SECRET dans .env.production
```

### Révoquer les tokens
```bash
# Si TOTP/2FA : regénérer les codes
php artisan tinker
# User::find(1)->forceFill(['two_factor_secret' => null])->save();
```

---

## 5. Test de Sécurité

### Test des Headers
```bash
curl -I https://votre-domaine.com

# Vérifier :
# - X-Content-Type-Options: nosniff ✓
# - X-Frame-Options: DENY ✓
# - X-XSS-Protection: 1; mode=block ✓
# - Strict-Transport-Security ✓
```

### Test Input Validation
```bash
# Essayer injection SQL (doit échouer)
curl -X POST https://votre-domaine.com/products/123/reviews \
  -d "author_name=test' OR '1'='1"

# Essayer XSS (doit être échappé)
curl -X POST https://votre-domaine.com/products/123/reviews \
  -d "author_name=<script>alert('xss')</script>"
```

### Test Rate Limiting
```bash
# Essayer +5 votes en moins d'une minute (doit être bloqué)
for i in {1..10}; do
  curl -X POST https://votre-domaine.com/products/123/vote
done
```

---

## 6. Gestion des Utilisateurs Admins

### Audit des admins
```bash
# Voir les utilisateurs avec accès admin
SELECT * FROM users WHERE is_admin = TRUE;

# Vérifier les derniers accès
SELECT * FROM users WHERE is_admin = TRUE ORDER BY updated_at DESC;
```

### Révoquer l'accès
```php
// Dans Tinker
User::find($admin_id)->update(['is_admin' => false]);
```

### Ajouter nouvel admin
```php
// Dans Tinker
User::create([
    'name' => 'Nouvel Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('mot_de_passe_fort'),
    'is_admin' => true,
    'email_verified_at' => now(),
]);
```

---

## 7. Vérifications Mensuelles

### Checklist de sécurité
- [ ] Vérifier composer audit (aucune vulnérabilité)
- [ ] Vérifier npm audit (aucune vulnérabilité)
- [ ] Consulter les logs pour anomalies
- [ ] Vérifier backups bien faits
- [ ] Tester rate limiting fonctionne
- [ ] Vérifier HTTPS certificat valide
- [ ] Vérifier DB password n'est pas en production
- [ ] Nettoyer les sessions expirées

```bash
# Automatiser le check
0 0 1 * * /usr/bin/php -r 'echo shell_exec("composer audit");' | mail -s "Security Audit" admin@example.com
```

---

## 8. Incident Response

### En cas de suspicion d'intrusion

1. **Isoler le système**
```bash
# Maintenir le site en mode maintenance
php artisan down --message="Maintenance de sécurité"
```

2. **Vérifier les logs**
```bash
# Chercher activité suspecte
grep -i "executable" storage/logs/*.log
grep -i "FAILED\|ERROR" storage/logs/*.log
```

3. **Sauvegarder les preuves**
```bash
tar -czf /forensics/backup_$(date +%s).tar.gz storage/logs/
```

4. **Réinitialiser les passwords**
```bash
# Tous les utilisateurs
php artisan tinker
User::all()->each->update(['password' => bcrypt('temp_new_password_')]);
```

5. **Redéployer**
```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate
php artisan optimize
```

---

## 9. Dépendances à Surveiller

### Packages critiques
- `laravel/framework` - Mises à jour de sécurité urgentes
- `illuminate/*` - Composants core
- `laravel/fortify` - Authentication
- `guzzlehttp/guzzle` - HTTP requests
- `symfony/*` - Components

### News de sécurité
- [Laravel Security Advisories](https://laravel.com/blog/security-releases)
- [Packagist Security Advisories](https://packagist.org/security-advisories)
- [CVE Database](https://cve.mitre.org/)

---

## 10. Configuration AlwaysData Spécifique

### Optimisations
```ini
# .htaccess pour AlwaysData
SetEnv HTTPS on
SetEnvIf X-Forwarded-Proto https HTTPS=on
SetEnvIf X-Forwarded-Proto https SERVER_PORT=443
```

### SSL/TLS
```
# AlwaysData supporte Let's Encrypt gratuit
# Admin panel → SSL → Certificate auto-renew
```

### Limites
```
# AlwaysData limits (important pour rate limiting)
PHP memory: 512MB
Upload max: 2GB
Execution time: 300s
```

---

## 📋 Checklist de Maintenance

```markdown
Hebdomadaire:
- [ ] Vérifier logs pour erreurs
- [ ] Vérifier espace disque

Mensuel:
- [ ] Audit de sécurité
- [ ] Mettre à jour packages
- [ ] Vérifier backups

Trimestriel:
- [ ] Revoir les permissions utilisateurs
- [ ] Tester disaster recovery
- [ ] Audit de code

Annuel:
- [ ] Audit de sécurité complet
- [ ] Penetration test
- [ ] Performance review
```

---

**Document mis à jour: 10 Avril 2026**
**Status: ✅ Production Ready - EX: 10/10**
