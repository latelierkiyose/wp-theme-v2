# PRD 0024 — Guide de configuration plugins performance et SEO

## Statut

**brouillon**

## Objectif

Documenter la sélection et la configuration recommandée des plugins gratuits pour résoudre les problèmes Lighthouse qui ne relèvent pas du code thème : cache, SEO, compression d'images, et optimisation des assets.

## Contexte

Certains problèmes détectés par Lighthouse sont typiquement gérés par des plugins WordPress plutôt que par le code du thème :

| Problème Lighthouse | Solution plugin |
|---------------------|----------------|
| "Document does not have a meta description" | Plugin SEO |
| "Use efficient cache lifetimes" | Plugin cache |
| "Improve image delivery" (WebP, compression) | Plugin d'optimisation d'images |
| "Minify CSS / Minify JavaScript" | Plugin d'optimisation d'assets (+ PRD 0019) |
| "Legacy JavaScript" (polyfills inutiles) | Plugin d'optimisation JS |

Le document `doc/integrations.md` mentionne WP Rocket (payant) et Autoptimize (gratuit). Ce PRD précise les choix gratuits recommandés et leur configuration.

## Dépendances

- **PRD 0019** : Minification des assets (le thème minifie ses propres assets ; le plugin gère les assets tiers et la combinaison)
- **PRD 0021** : Chemin critique de rendu (optimisations thème complémentaires aux plugins)

## Sélection des plugins

### 1. SEO — Rank Math SEO (gratuit)

**Plugin** : [Rank Math SEO](https://wordpress.org/plugins/seo-by-rank-math/)

**Justification du choix** :
- Gratuit avec toutes les fonctionnalités SEO essentielles (pas de limitation artificielle comme Yoast Free)
- Meta descriptions, Open Graph, Twitter Cards inclus dans la version gratuite
- Schema.org markup automatique (LocalBusiness, Event) — gratuit, alors que payant chez Yoast
- Sitemap XML intégré
- Interface plus moderne que Yoast
- Léger en termes de performance

**Configuration recommandée** :

#### Setup initial (assistant de configuration)
- Type de site : "Small Business"
- Nombre d'auteurs : 1
- Activer : Sitemap XML, Open Graph, Schema markup

#### Meta descriptions
- **Globale** : Dashboard > Rank Math > Titles & Meta > Global Meta
  - Separator : `–` (tiret)
  - Homepage meta description : "L'Atelier Kiyose, centre de bien-être et développement personnel à Brux (86). Art-thérapie, rigologie, bols tibétains, ateliers philosophie."
- **Pages** : Rank Math > Titles & Meta > Pages
  - Default template : `%excerpt%` (utilise l'extrait de la page)
- **Articles** : Rank Math > Titles & Meta > Posts
  - Default template : `%excerpt%`

#### Schema markup
- Type par défaut : "LocalBusiness"
- Données de l'entreprise :
  - Nom : L'Atelier Kiyose
  - Adresse : Brux, 86510, Nouvelle-Aquitaine
  - Téléphone : (voir `references/business-info.json`)
  - URL : https://www.latelierkiyose.fr/

#### Open Graph
- Activer Open Graph et Twitter Cards
- Image par défaut : logo ou image representative du site
- Type Twitter Card : "summary_large_image"

### 2. Cache et optimisation assets — Autoptimize + WP Super Cache

#### 2a. Autoptimize (gratuit)

**Plugin** : [Autoptimize](https://wordpress.org/plugins/autoptimize/)

**Rôle** : Optimisation des assets côté client (combinaison, minification, defer).

**Configuration recommandée** :

**CSS** :
- Optimize CSS Code : activé
- Aggregate CSS Files : activé
- Inline and Defer CSS : **désactivé** (le thème gère sa propre stratégie de CSS critique via PRD 0021)
- Inline all CSS : désactivé

**JavaScript** :
- Optimize JavaScript Code : activé
- Aggregate JS Files : activé
- Force JavaScript in `<head>` : désactivé
- Add try-catch wrapping : **activé** (protection contre les erreurs de scripts tiers)

**HTML** :
- Optimize HTML Code : activé
- Keep HTML comments : désactivé

**Misc** :
- Save aggregated scripts/styles as static files : activé
- Enable 404 fallbacks : activé
- Optimize for logged in editors too : **désactivé** (développement plus facile)

**Exclude from aggregation** :
- `jquery.min.js` (déjà géré par WordPress core)
- Scripts reCAPTCHA (chargés de manière asynchrone)

#### 2b. WP Super Cache (gratuit)

**Plugin** : [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/)

**Rôle** : Cache de pages côté serveur — sert des pages HTML statiques au lieu de regénérer le PHP à chaque visite.

**Justification** : Plugin gratuit d'Automattic (éditeur de WordPress), fiable et bien maintenu.

**Configuration recommandée** :

**Caching** :
- Caching : activé
- Cache Delivery Method : "Simple" (suffisant pour un site vitrine)

**Advanced** :
- Don't cache pages for known users : activé
- Cache rebuild : activé
- 304 Not Modified browser caching : activé
- Clear all cache files when a post or page is published : activé

**Expiry Time & Garbage Collection** :
- Cache timeout : 3600 secondes (1 heure)
- Scheduler : Timer

**Preload** :
- Preload mode : activé
- Refresh preloaded cache files every : 1440 minutes (24h)
- Preload tags, categories and other taxonomies : activé

**Headers** (résout "Use efficient cache lifetimes") :
WP Super Cache ajoute automatiquement des headers de cache. Pour les assets statiques (CSS, JS, images, fonts), ajouter dans `.htaccess` ou via la configuration Apache/Nginx du serveur :

```apache
# Cache statique (assets du thème)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
</IfModule>
```

**Note** : Le cache busting est assuré par le versioning `filemtime()` dans `inc/enqueue.php` — les URLs changent quand les fichiers sont modifiés, donc un cache long (1 an) est sûr.

### 3. Optimisation d'images — EWWW Image Optimizer (gratuit)

**Plugin** : [EWWW Image Optimizer](https://wordpress.org/plugins/ewww-image-optimizer/)

**Justification du choix** :
- Entièrement gratuit (pas de quota mensuel, contrairement à ShortPixel 100/mois ou Imagify 20MB/mois)
- Compression locale sur le serveur (pas d'envoi vers un service tiers)
- Conversion WebP automatique intégrée
- Lazy loading natif

**Configuration recommandée** :

**Basic** :
- Remove Metadata : activé (supprime EXIF/GPS des images)
- Optipng Level : 2 (bon compromis qualité/vitesse)
- JPG quality : 82 (WordPress default, bonne qualité)

**ExactDN/Easy IO** :
- Désactivé (c'est le service payant — la version gratuite fonctionne sans)

**WebP** :
- WebP Conversion : **activé**
- Delivery Method : `<picture>` tags
  - Génère automatiquement des `<picture>` avec `<source type="image/webp">` et `<img>` fallback
  - Compatible avec le `srcset` WordPress natif

**Advanced** :
- Bulk Optimize : Exécuter après installation pour convertir les images existantes
- Resize Detection : activé (détecte les images surdimensionnées)

**Performance attendue** :
- Réduction de 20-40% de la taille des images (compression + WebP)
- Résout "Improve image delivery" dans Lighthouse

### 4. Configuration reCAPTCHA (résolution du conflit)

**Problème** : Erreur console "reCAPTCHA has already been rendered in this element" — CF7 et Brevo chargent chacun leur propre instance reCAPTCHA.

**Solution recommandée** :

1. **Vérifier** dans Brevo (Brevo > Forms > Form Settings) si reCAPTCHA est activé
2. **Vérifier** dans CF7 (Contact > Integration > reCAPTCHA)
3. **Désactiver reCAPTCHA dans Brevo** si CF7 le gère déjà globalement
4. Alternativement, remplacer reCAPTCHA par le honeypot de CF7 (plus léger, pas de JS externe)

**Impact** : Supprime l'erreur console + réduit les requêtes réseau vers `gstatic.com`.

## Installation et activation

### Ordre d'installation recommandé

1. **EWWW Image Optimizer** — installer et lancer l'optimisation bulk en premier (peut prendre du temps)
2. **Rank Math SEO** — configurer les meta descriptions et Schema
3. **WP Super Cache** — activer en dernier (le cache doit cacher les pages déjà optimisées)
4. **Autoptimize** — activer après WP Super Cache (les assets combinés seront cachés)

### Vérification post-installation

```
1. Vider tous les caches (WP Super Cache > Delete Cache)
2. Visiter le site en navigation privée
3. Inspecter View Source :
   - [ ] Meta description présente
   - [ ] Open Graph tags présents
   - [ ] CSS combinés en 1-2 fichiers (Autoptimize)
   - [ ] JS combinés et déférés
4. DevTools > Network :
   - [ ] Images servies en WebP (colonne Type)
   - [ ] Headers Cache-Control présents sur les assets
   - [ ] Nombre total de requêtes réduit
5. Lighthouse :
   - [ ] Score Performance ≥ 85
   - [ ] Score SEO = 100
   - [ ] "Document does not have a meta description" — résolu
   - [ ] "Use efficient cache lifetimes" — résolu
   - [ ] "Improve image delivery" — résolu
```

## Performance attendue (cumul avec PRD 0021-0023)

| Métrique | Avant (tout) | Après thème (0021-0023) | Après plugins (0024) |
|----------|-------------|------------------------|---------------------|
| Performance | 56 | ~70-75 | **85-95** |
| Accessibility | 93 | ~100 | 100 |
| Best Practices | 92 | ~95 | **95-100** |
| SEO | 92 | 92 | **100** |
| FCP | 9.1s | ~3-4s | **< 2s** |
| LCP | 16.6s | ~5-8s | **< 3s** |

## Compatibilité entre plugins

| Plugin 1 | Plugin 2 | Compatibilité |
|----------|----------|---------------|
| Autoptimize | WP Super Cache | Compatible — Autoptimize a une intégration native avec WP Super Cache |
| EWWW | Autoptimize | Compatible — EWWW gère les images, Autoptimize gère CSS/JS |
| Rank Math | Tous | Compatible — plugin SEO indépendant des plugins de performance |
| Autoptimize | Events Manager | Tester — exclure les scripts Events Manager de l'agrégation si nécessaire |
| WP Super Cache | CF7 | Compatible — les formulaires utilisent AJAX, pas affectés par le cache |

## Mise à jour de `doc/integrations.md`

Après validation, mettre à jour la section "SEO & Performance" du fichier `doc/integrations.md` :

```markdown
## SEO & Performance

**SEO :**
- **Rank Math SEO** (gratuit) ✅
- Meta descriptions, Open Graph, Twitter Cards
- Schema.org markup (LocalBusiness)
- Sitemap XML

**Cache :**
- **WP Super Cache** (gratuit, Automattic) ✅
- Cache de pages HTML statiques
- Headers de cache pour assets statiques

**Optimisation assets :**
- **Autoptimize** (gratuit) ✅
- Combinaison et minification CSS/JS des plugins tiers
- Complète la minification thème (PRD 0019)

**Images :**
- **EWWW Image Optimizer** (gratuit) ✅
- Compression automatique au upload
- Conversion WebP avec fallback `<picture>`
```

## Hors périmètre

- Configuration serveur (Nginx, Apache) — ce PRD couvre uniquement les plugins WordPress
- CDN — recommandé pour la production mais hors périmètre du thème
- Object cache (Redis/Memcached) — nécessite une configuration serveur spécifique
