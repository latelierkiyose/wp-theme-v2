# L'Atelier Kiyose - Thème WordPress v2

## Contexte

L'Atelier Kiyose est un centre de bien-être et développement personnel situé à Brux (86510, Nouvelle-Aquitaine), animé par Virginie Le Mignon ("Happycultrice"). Le site propose 4 piliers de services:

1. **Art-thérapie** - Ateliers créatifs (théâtre, danse, arts plastiques, kintsugi)
2. **Rigologie / Yoga du rire** - Techniques psychocorporelles de libération émotionnelle
3. **Bols tibétains** - Sonothérapie et rééquilibrage énergétique
4. **Ateliers philosophie** - Débats démocratiques pour enfants et adolescents

**Public cible principal**: Femmes adultes (art-thérapie, rigologie, bols tibétains, ateliers créatifs)
**Public secondaire**: Enfants et adolescents (ateliers philosophie) — le ton et le design des pages philo doivent rester accessibles à ce public plus jeune

Site actuel: https://www.latelierkiyose.fr/
Ancien thème: https://github.com/latelierkiyose/wp-theme


## Documentation détaillée

Pour plus d'informations, consulter les documents suivants dans `doc/`:
- **[Objectif de la refonte](doc/goal.md)** - Problèmes à résoudre et critères de succès
- **[Fonctionnalités](doc/features.md)** - Détail complet des pages et fonctionnalités
- **[Design System](doc/design-system.md)** - Palette de couleurs, typographie, composants UI, identité Kintsugi
- **[Ton et voix](doc/tone.md)** - Positionnement, valeurs, style éditorial
- **[Plugins & Intégrations](doc/integrations.md)** - Events Manager, Brevo, CF7, SEO, sécurité


## Environnement de développement local

### Lancer WordPress en local

Le projet utilise Docker Compose pour démarrer un environnement WordPress complet en local.

**Commande:**
```sh
docker compose up
```

**Accès:**
- Site WordPress: `http://127.0.0.1:8000`
- Interface d'administration: `http://127.0.0.1:8000/wp-admin`

**Notes:**
- Le thème est monté dans le container et accessible immédiatement
- Les modifications de fichiers sont reflétées en temps réel (pas de rebuild nécessaire)
- Pour arrêter l'environnement: `docker compose down`


## Architecture Technique

### Type de thème
**Thème WordPress classique** avec templates PHP (pas de Block Theme/FSE).

### Structure des fichiers
```
wp-theme-v2/
└── latelierkiyose/        # Thème WordPress
    ├── style.css              # Stylesheet principal + métadonnées du thème
    ├── screenshot.png         # Capture d'écran du thème (1200x900px recommandé)
    ├── functions.php          # Fonctions et hooks du thème (charge les fichiers inc/)
    ├── index.php              # Template par défaut (fallback)
    ├── header.php             # En-tête du site
    ├── footer.php             # Pied de page
    ├── 404.php                # Page d'erreur 404
    ├── search.php             # Résultats de recherche
    │
    ├── templates/             # Templates de pages (avec header "Template Name:")
    │   ├── page-home.php         # Page d'accueil
    │   ├── page-services.php     # Template services génériques
    │   ├── page-contact.php      # Page contact
    │   ├── page-about.php        # Page à propos
    │   └── page-calendar.php     # Calendrier/Tarifs
    │
    ├── single.php             # Template article unique
    ├── archive.php            # Template archives blog
    ├── page.php               # Template page par défaut
    │
    ├── assets/
    │   ├── css/
    │   │   ├── main.css          # Styles principaux (mobile-first, media queries colocalisées)
    │   │   └── components/       # Composants réutilisables
    │   ├── js/
    │   │   ├── main.js           # JavaScript principal
    │   │   └── modules/          # Modules JS
    │   ├── fonts/                # Polices auto-hébergées (RGPD)
    │   └── images/               # Images du thème
    │
    ├── inc/                   # Fonctionnalités PHP modulaires
    │   ├── setup.php             # Configuration du thème
    │   ├── enqueue.php           # Chargement des assets
    │   ├── custom-post-types.php # CPT Témoignages
    │   ├── customizer.php        # Options du customizer
    │   └── accessibility.php     # Fonctions accessibilité
    │
    ├── template-parts/        # Parties de templates réutilisables
    │   ├── content-service.php   # Affichage d'un service
    │   ├── content-testimony.php # Affichage témoignage
    │   └── content-blog.php      # Affichage article blog
    │
    └── tests/                 # Tests unitaires PHP
```

### Stack technique
- **PHP**: 8.1+ (dernière version stable)
- **WordPress**: 6.7+ (dernière version)
- **CSS**: CSS natif moderne (variables CSS, Grid, Flexbox) - Pas de framework lourd
- **JavaScript**: Vanilla JS ES6+ - Bibliothèques minimales uniquement si nécessaire
- **Build tools**: Optionnel (évaluer si npm/webpack nécessaire ou CSS/JS natif suffisant)

### Templates WordPress utilisés
| Template | Usage |
|----------|-------|
| `index.php` | Fallback général |
| `header.php` | En-tête avec navigation |
| `footer.php` | Pied de page avec réseaux sociaux |
| `page.php` | Pages standards |
| `single.php` | Articles de blog |
| `archive.php` | Archives blog |
| `search.php` | Résultats de recherche |
| `404.php` | Page d'erreur 404 |
| `templates/page-*.php` | Pages spécifiques (assignées via `Template Name:` dans l'admin WP) |

> **Note** : Les fichiers dans `templates/` ne bénéficient pas de la résolution automatique par slug WordPress. Chaque fichier doit contenir un header `/* Template Name: Nom du template */` et être assigné manuellement dans l'éditeur de page WordPress.

### Conventions
- **Prefixe**: `kiyose_` pour toutes les fonctions PHP
- **Text domain**: `kiyose` (pas d'i18n nécessaire pour ce projet)
- **Hooks**: Utiliser les hooks WordPress standard, créer des hooks custom si nécessaire
- **CSS BEM**: Utiliser la méthodologie BEM pour les classes CSS
- **Chargement modulaire**: `functions.php` charge les fichiers `inc/*.php` via `require_once get_template_directory() . '/inc/fichier.php';`


## Standards & Contraintes

### WordPress Coding Standards (WPCS)

**Obligatoire pour tout le code PHP:**
- Suivre les [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Indentation: tabs (pas d'espaces)
- Nommage des fonctions: `kiyose_function_name()`
- Nommage des hooks: `kiyose_hook_name`
- Validation avec PHP_CodeSniffer + WordPress rulesets
- Sécurité:
  - Sanitize toutes les entrées utilisateur (`sanitize_text_field()`, `esc_url()`, etc.)
  - Escape toutes les sorties (`esc_html()`, `esc_attr()`, `esc_url()`)
  - Utiliser nonces pour les formulaires (`wp_nonce_field()`, `wp_verify_nonce()`)
  - Prepared statements pour les requêtes DB (`$wpdb->prepare()`)

### Accessibilité WCAG 2.2 AA (Priorité absolue)

**Exigences strictes:**
- ✅ Contraste de couleurs minimum 4.5:1 (texte normal), 3:1 (texte large)
- ✅ Navigation clavier complète (Tab, Enter, Esc, flèches)
- ✅ Focus visible sur tous les éléments interactifs
- ✅ Landmarks ARIA (`<nav>`, `<main>`, `<aside>`, `<footer>`)
- ✅ Textes alternatifs pour toutes les images (`alt=""`)
- ✅ Labels explicites pour tous les champs de formulaire
- ✅ Messages d'erreur clairs et associés aux champs
- ✅ Skip links pour navigation rapide
- ✅ Headings hiérarchiques (`<h1>` unique, puis `<h2>`, `<h3>`, etc.)
- ✅ Links descriptifs (pas de "cliquez ici")
- ✅ Responsive jusqu'à 320px de largeur
- ✅ Zoom jusqu'à 200% sans perte de fonctionnalité
- ✅ Pas de dépendance uniquement couleur pour l'information
- ✅ Animations respectant `prefers-reduced-motion`

**Outils de validation:**
- axe DevTools
- WAVE browser extension
- Lighthouse accessibility audit
- Tests manuels au clavier
- Tests avec lecteur d'écran (NVDA/VoiceOver)

### Mobile-First & Responsive

**Approche obligatoire:**
- Concevoir d'abord pour mobile (320px+)
- Progressive enhancement vers desktop
- Breakpoints suggérés:
  - Mobile: 320px - 767px
  - Tablet: 768px - 1023px
  - Desktop: 1024px+
- Images responsives (`srcset`, `sizes`)
- Touch targets minimum 44x44px (WCAG 2.2)
- Pas de scroll horizontal
- Container principal avec `max-width: 1200px` pour limiter la longueur des lignes sur grands écrans
- Menus hamburger accessibles sur mobile (voir section Navigation)

### Performance

**Objectifs Core Web Vitals:**
- LCP (Largest Contentful Paint): < 2.5s
- INP (Interaction to Next Paint): < 200ms
- CLS (Cumulative Layout Shift): < 0.1

**Optimisations requises:**
- Minification CSS/JS (en production)
- Images optimisées (WebP avec fallback)
- Lazy loading pour images below-the-fold
- Chargement différé JS non-critique (`defer`, `async`)
- CSS critique inline pour above-the-fold
- Pas de jQuery (sauf si absolument nécessaire)
- Limiter les requêtes HTTP
- Utiliser le cache navigateur

### Tests

**Tests obligatoires:**
- ✅ Tests unitaires PHP (PHPUnit) pour fonctions critiques
- ✅ Tests d'accessibilité automatisés (axe, pa11y)
- ✅ Tests manuels sur:
  - Chrome, Firefox, Safari (dernières versions)
  - iOS Safari, Chrome Android
  - Navigation clavier complète
  - Zoom 200%
- ✅ Tests de performance (Lighthouse, PageSpeed Insights)
- ✅ Validation W3C HTML



