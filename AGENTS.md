# L'Atelier Kiyose - Thème WordPress v2

## Contexte

L'Atelier Kiyose est un centre de bien-être et développement personnel situé à Brux (86510, Nouvelle-Aquitaine), animé par Virginie Le Mignon ("Happycultrice"). Le site propose 4 piliers de services:

1. **Art-thérapie** - Ateliers créatifs (théâtre, danse, arts plastiques, kintsugi)
2. **Rigologie / Yoga du rire** - Techniques psychocorporelles de libération émotionnelle
3. **Bols tibétains** - Sonothérapie et rééquilibrage énergétique
4. **Ateliers philosophie** - Débats démocratiques pour enfants et adolescents

Site actuel: https://www.latelierkiyose.fr/
Ancien thème: https://github.com/latelierkiyose/wp-theme

## Objectif de la refonte

**Refonte complète du thème WordPress en repartant de zéro.**

### Problèmes du thème actuel à résoudre:
- ❌ **Accessibilité**: Non conforme WCAG 2.2 AA
- ❌ **Design/UX**: Interface dépassée, pas assez créatif/coloré
- ❌ **Code**: Mal structuré, difficile à maintenir
- ❌ **Performance**: Vitesse insuffisante
- ❌ **Responsive**: Espace mal utilisé, pas mobile-first

### Critères de succès:
- ✅ Conformité WCAG 2.2 niveau AA
- ✅ Design créatif et coloré reflétant l'identité de l'atelier
- ✅ Code propre suivant WordPress Coding Standards
- ✅ Performance optimale (Core Web Vitals)
- ✅ Mobile-first, responsive parfait
- ✅ Maintenabilité et extensibilité du code

## Architecture Technique

### Type de thème
**Thème WordPress classique** avec templates PHP (pas de Block Theme/FSE).

### Structure des fichiers
```
wp-theme-v2/
├── style.css              # Stylesheet principal + métadonnées du thème
├── functions.php          # Fonctions et hooks du thème
├── index.php              # Template par défaut
├── header.php             # En-tête du site
├── footer.php             # Pied de page
├── sidebar.php            # Barre latérale (si nécessaire)
│
├── templates/             # Templates de pages
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
│   │   ├── main.css          # Styles principaux
│   │   ├── components/       # Composants réutilisables
│   │   └── responsive.css    # Media queries
│   ├── js/
│   │   ├── main.js           # JavaScript principal
│   │   └── modules/          # Modules JS
│   └── images/               # Images du thème
│
├── inc/                   # Fonctionnalités PHP modulaires
│   ├── setup.php             # Configuration du thème
│   ├── enqueue.php           # Chargement des assets
│   ├── custom-post-types.php # CPT si nécessaire
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
| `templates/page-*.php` | Pages spécifiques (home, services, contact, etc.) |

### Conventions
- **Prefixe**: `kiyose_` pour toutes les fonctions PHP
- **Text domain**: `kiyose` (pas d'i18n nécessaire pour ce projet)
- **Hooks**: Utiliser les hooks WordPress standard, créer des hooks custom si nécessaire
- **CSS BEM**: Utiliser la méthodologie BEM pour les classes CSS

## Fonctionnalités

### 1. Page d'accueil (Homepage)

**Template**: `templates/page-home.php`

**Sections:**
- **Hero** - Présentation de Virginie et de l'atelier avec CTA principal
- **Les 4 Piliers** - Cartes cliquables vers chaque service (Art-thérapie, Rigologie, Bols tibétains, Philo)
- **Prochaines dates** - Widget Events Manager avec 3-4 prochains événements
- **Témoignages** - Carousel ou grille de 3 témoignages récents
- **Newsletter** - Formulaire d'inscription Brevo (email, prénom, nom optionnel)
- **Actualités** - 3 derniers articles du blog

### 2. Pages Services (4 piliers)

**Templates**: Pages individuelles avec `page.php` ou template custom par service

**Structure commune:**
- Titre du service avec sous-titre descriptif
- Description détaillée (plusieurs paragraphes)
- Bénéfices / Approche / Méthodologie
- Formats et durées proposés
- Tarifs indicatifs
- CTA vers contact/calendrier
- Galerie photos (optionnel)

**Services à créer:**
1. **Art-thérapie** - Programmes spécialisés (Burn-out, Deuil, "Oser être soi", Enfant intérieur)
2. **Rigologie/Yoga du rire** - Include "Silence Kiyose" (expérience casques audio)
3. **Bols tibétains** - Journées sonores + massage individuel
4. **Ateliers Philosophie** - Format débat + variante "Philo-Art"

### 3. Calendrier & Tarifs

**Template**: `templates/page-calendar.php`

**Contenu:**
- Intégration **Events Manager** (plugin déjà utilisé)
- Affichage des événements avec:
  - Date et heure
  - Type d'atelier
  - Lieu
  - Tarif
  - Places disponibles
  - Bouton "Réserver"
- **Note importante**: Pas de paiement en ligne, réservation seulement

### 4. Blog / Actualités

**Templates**: `archive.php`, `single.php`

**Fonctionnalités:**
- Archive des articles avec pagination
- Catégories (Conseils, Annonces, Réflexions, etc.)
- Article unique avec date de publication
- Partage sur réseaux sociaux
- Pas de système de commentaires (à décider)

### 5. Témoignages

**Template**: `page.php` avec template part `content-testimony.php`

**Affichage:**
- Cartes de citations
- Attribution (prénom, contexte: individu/entreprise/structure)
- Peut utiliser Custom Post Type "Témoignage" pour faciliter la gestion

### 6. À Propos

**Template**: `templates/page-about.php`

**Contenu:**
- Biographie de Virginie Le Mignon
- Philosophie Kintsugi (métaphore importante)
- Parcours professionnel (20 ans d'expérience)
- Diplômes et certifications
- Photo(s) de Virginie

### 7. Contact

**Template**: `templates/page-contact.php`

**Éléments:**
- Formulaire de contact avec validation
  - Nom (requis)
  - Email (requis)
  - Message (requis)
  - Protection reCAPTCHA ou honeypot
- Informations de contact:
  - Adresse: Le Grand Vron, 86510 Brux
  - Téléphone: 06 58 37 32 05
  - SIRET: 49219620900026
- Liens réseaux sociaux
- Carte Google Maps (optionnel)

### 8. Newsletter

**Emplacement**: Footer + Homepage

**Champs:**
- Email (requis)
- Prénom (requis)
- Nom (optionnel)
- Intégration avec **Brevo** (plugin déjà utilisé)
- Conformité RGPD

### 9. Navigation & Footer

**Navigation principale:**
- Utiliser `wp_nav_menu()` pour navigation dynamique
- Emplacement de menu: `primary` (à déclarer dans `functions.php`)
- Support de 2 niveaux:
  - **Niveau 1**: Items visibles dans la barre de navigation
  - **Niveau 2**: Items apparaissant au survol (dropdown menu)
- Tous les items sont cliquables (pas de séparateurs non-cliquables)
- Configuration via WordPress Admin: `/wp-admin/nav-menus.php`
- Accessible au clavier (WCAG 2.2 AA)

**Footer:**
- Liens réseaux sociaux (Facebook, Instagram, LinkedIn @latelierkiyose)
- Newsletter Brevo
- Mentions légales / Politique de confidentialité
- Copyright
- SIRET

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
- Menus hamburger accessibles sur mobile

### Performance

**Objectifs Core Web Vitals:**
- LCP (Largest Contentful Paint): < 2.5s
- FID (First Input Delay): < 100ms
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

## Design System

### Palette de couleurs

**Couleurs principales (à conserver de l'identité actuelle):**
- **Rose pêche clair**: `#FCEBD2` - Backgrounds, sections
- **Bourgogne/Rose foncé**: `#F4BFBF` - Accents, boutons, liens
- **À définir**: Couleurs complémentaires pour textes, bordures, états (hover, focus, disabled)

**Exigences accessibilité:**
- Tous les textes doivent avoir contraste ≥ 4.5:1 sur leur fond
- Boutons et éléments interactifs ≥ 3:1
- États focus visibles avec contraste suffisant
- Utiliser un outil comme [Contrast Checker](https://webaim.org/resources/contrastchecker/) pour valider

**Variables CSS recommandées:**
```css
:root {
  --color-primary: #F4BFBF;      /* Bourgogne/rose */
  --color-secondary: #FCEBD2;     /* Rose pêche clair */
  --color-text: #333333;          /* Texte principal */
  --color-text-light: #666666;    /* Texte secondaire */
  --color-background: #FFFFFF;    /* Fond principal */
  --color-border: #E0E0E0;        /* Bordures */
  /* ... autres couleurs à définir */
}
```

### Typographie

**Approche:**
- **Titre (serif)**: Police élégante et chaleureuse (ex: Playfair Display, Lora, Crimson Text)
- **Corps (sans-serif)**: Police lisible et moderne (ex: Inter, Open Sans, Nunito)
- Toutes les polices doivent être optimisées (subset, preload si nécessaire)

**Échelle typographique:**
```css
:root {
  --font-size-xs: 0.75rem;    /* 12px */
  --font-size-sm: 0.875rem;   /* 14px */
  --font-size-base: 1rem;     /* 16px - taille minimum pour body */
  --font-size-lg: 1.125rem;   /* 18px */
  --font-size-xl: 1.25rem;    /* 20px */
  --font-size-2xl: 1.5rem;    /* 24px */
  --font-size-3xl: 2rem;      /* 32px */
  --font-size-4xl: 2.5rem;    /* 40px */
}
```

**Line height:**
- Texte corps: 1.6-1.8 (confort de lecture)
- Titres: 1.2-1.4

### Espacement

**Système d'espacement cohérent:**
```css
:root {
  --spacing-xs: 0.25rem;   /* 4px */
  --spacing-sm: 0.5rem;    /* 8px */
  --spacing-md: 1rem;      /* 16px */
  --spacing-lg: 1.5rem;    /* 24px */
  --spacing-xl: 2rem;      /* 32px */
  --spacing-2xl: 3rem;     /* 48px */
  --spacing-3xl: 4rem;     /* 64px */
}
```

**Principe**: Espacement généreux pour sensation de calme et bien-être

### Composants UI principaux

**Boutons:**
- **Primary**: Fond bourgogne/rose, texte blanc, border-radius arrondi
- **Secondary**: Outline bourgogne/rose, fond transparent
- **States**: Hover (assombrir 10%), Focus (outline visible), Active, Disabled
- Taille minimum: 44x44px (touch target WCAG 2.2)

**Cartes (Services, Témoignages):**
- Fond blanc ou rose pêche très clair
- Ombre légère (box-shadow subtile)
- Border-radius arrondi
- Padding généreux
- Effet hover subtil (élévation, scale léger)

**Formulaires:**
- Labels visibles au-dessus des champs (pas de placeholder seul)
- Bordures claires, focus visible
- Messages d'erreur en rouge accessible (≥ 4.5:1 contraste)
- Icônes de validation (✓ vert, ✗ rouge)

**Navigation dropdown:**
- Animation douce (transition 200-300ms)
- Flèche indicative pour items avec sous-menu
- Accessible clavier (Tab, Enter, Esc, flèches)
- Focus trap dans le dropdown ouvert

### Animations & Transitions

**Principes:**
- Animations subtiles et rapides (200-300ms)
- Respecter `prefers-reduced-motion` (désactiver animations si activé)
- Pas d'animations automatiques qui distraient
- Transitions sur hover/focus pour feedback utilisateur

**Exemples:**
```css
@media (prefers-reduced-motion: no-preference) {
  .button {
    transition: background-color 0.2s ease;
  }
  .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
}
```

### Images & Médias

**Formats:**
- WebP avec fallback JPG/PNG
- SVG pour icônes et logos
- Optimisation avant upload (TinyPNG, Squoosh)

**Responsive images:**
```html
<img
  src="image-800w.webp"
  srcset="image-400w.webp 400w, image-800w.webp 800w, image-1200w.webp 1200w"
  sizes="(max-width: 768px) 100vw, 50vw"
  alt="Description accessible"
  loading="lazy"
>
```

### Iconographie

- Utiliser des icônes simples et reconnaissables
- Cohérence de style (outline vs filled)
- Toujours accompagnées de labels textuels (ou `aria-label`)
- Couleur respectant le contraste minimum

## Plugins & Intégrations

### Calendrier & Réservations (En place)

**Events Manager** ✅
- Plugin déjà utilisé pour la gestion des événements et réservations
- Pas de paiement en ligne (réservation uniquement)

**Intégration thème:**
- Shortcode Events Manager dans `templates/page-calendar.php`
- Widget homepage pour afficher les prochains événements
- Personnalisation CSS pour matcher le design du thème
- Templates Events Manager custom dans `/wp-content/themes/wp-theme-v2/plugins/events-manager/` si nécessaire

### Newsletter (En place)

**Brevo** (anciennement Sendinblue) ✅
- Plugin déjà utilisé pour la newsletter
- Formulaire dans footer + homepage
- Champs: Email (requis), Prénom (requis), Nom (optionnel)
- Double opt-in RGPD conforme

### Formulaires Contact

**Contact Form 7** ou **WPForms** (Lite)
- Formulaire contact personnalisé
- Protection anti-spam (reCAPTCHA ou honeypot)
- Validation accessible
- Hooks pour styling custom

### SEO & Performance

**SEO:**
- **Yoast SEO** ou **Rank Math**
- Métadonnées optimisées
- Sitemap XML
- Schema.org markup

**Performance:**
- **WP Rocket** (recommandé) ou **Autoptimize** (gratuit)
- Minification CSS/JS/HTML
- Cache
- Lazy loading
- **ShortPixel** ou **Imagify** - Compression images automatique

### Sécurité

**Wordfence Security** ou **iThemes Security**
- Protection contre attaques
- Firewall
- Scan malware
- 2FA pour admin

### Réseaux sociaux

**Custom implementation recommandée**
- Liens footer: Facebook, Instagram, LinkedIn (@latelierkiyose)
- SVG icons optimisés
- Accessible (`aria-label`)

### Backup

**UpdraftPlus** ou **BackWPup**
- Sauvegardes automatiques
- Stockage cloud
- Restauration facile

### Configuration WordPress recommandée

**Permaliens:** `/%postname%/` (SEO-friendly)

**Lecture:**
- Page d'accueil statique
- Page blog dédiée pour actualités

### Intégrations tierces

**reCAPTCHA:** v3 (invisible) pour formulaires

**RGPD:**
- **Complianz** ou **Cookie Notice** plugin
- Bannière cookies
- Politique de confidentialité

### Notes importantes

- ⚠️ **Limiter le nombre de plugins** - Chaque plugin ajoute du poids et des risques de sécurité
- ⚠️ **Vérifier la compatibilité** - Tous les plugins doivent être compatibles avec WordPress 6.7+
- ⚠️ **Mises à jour régulières** - Maintenir tous les plugins à jour pour sécurité et performance
- ⚠️ **Tests après installation** - Tester accessibilité et performance après chaque ajout de plugin
