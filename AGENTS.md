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

## Ton et voix de la marque

**Positionnement**: Professionnelle de la joie et de la créativité

**Valeurs fondamentales**:
- **Authenticité** - Être vrai, sans masque, dans sa vulnérabilité
- **Originalité** - Approches créatives et uniques, hors des sentiers battus
- **Connexion** - Lien profond avec soi-même et avec les autres
- **Rire** - Légèreté et libération par le jeu et l'humour
- **Créativité** - Expression et transformation par le processus artistique
- **Alchimie** - Transformation profonde par le processus créatif, transmutation des blessures en lumière

**Ton éditorial**:
- Chaleureux et accueillant, créant un espace sécurisant
- Joyeux sans être superficiel, équilibre entre légèreté et profondeur
- Professionnel mais accessible, pas de jargon technique inutile
- Créatif et poétique, utiliser des métaphores (vitrail, lumière, fêlures, or)
- Équilibre entre sérieux (expertise, qualifications) et légèreté (rire, jeu)

**Style d'écriture**:
- Phrases courtes et claires
- Vocabulaire positif et transformationnel
- Utiliser le "vous" pour s'adresser directement aux femmes
- Métaphore Kintsugi récurrente: imperfections → beauté, fêlures → lumière
- CTAs invitants et doux (ex: "Venez explorer", "Laissez passer votre lumière")

**Exemples de messaging**:
- Hero tagline: "Transformez vos fêlures en lumière" ou "L'art de révéler votre beauté intérieure"
- Services: "Un espace pour explorer, créer, rire et transformer"
- À Propos: Développer l'histoire personnelle de Virginie avec authenticité et vulnérabilité

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
  - Lieu (Brux / Poitiers / En ligne)
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
- Commentaires désactivés (`remove_post_type_support( 'post', 'comments' )` dans `setup.php`)

### 5. Témoignages

**Template**: `page.php` avec template part `content-testimony.php`

**Affichage:**
- Cartes de citations
- Attribution (prénom, contexte: individu/entreprise/structure)

**Implémentation**: Custom Post Type `kiyose_testimony` (déclaré dans `inc/custom-post-types.php`)
- Champs: citation (contenu), prénom, contexte (individu/entreprise/structure)
- Pas d'archive publique dédiée (affichés via template parts sur homepage et page témoignages)
- Query custom dans les templates pour afficher les témoignages

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
  - Cabinet principal: Le Grand Vron, 86510 Brux
  - Ateliers également proposés à Poitiers
  - Séances en ligne disponibles
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

**Menu mobile (hamburger):**
- Bouton hamburger avec `aria-expanded="false/true"` et `aria-controls="mobile-menu"`
- Ouverture: overlay plein écran ou slide-in depuis la droite
- Focus trap activé quand le menu est ouvert (Tab circule uniquement dans le menu)
- `body` scroll verrouillé quand le menu est ouvert (`overflow: hidden`)
- Fermeture: bouton close, touche Esc, ou clic sur overlay
- Retour du focus sur le bouton hamburger à la fermeture
- Transition: 200-300ms, respecte `prefers-reduced-motion`

**Footer:**
- Liens réseaux sociaux (Facebook, Instagram, LinkedIn @latelierkiyose)
- Newsletter Brevo
- Mentions légales / Politique de confidentialité
- Copyright
- SIRET

### 10. Page 404

**Template**: `404.php`

**Contenu:**
- Message clair indiquant que la page n'existe pas
- Lien vers la page d'accueil
- Formulaire de recherche
- Suggestion de pages populaires (services, calendrier)
- Design cohérent avec le reste du site (pas une page "technique")

### 11. Recherche

**Template**: `search.php`

**Contenu:**
- Formulaire de recherche avec champ et bouton accessible
- Liste des résultats avec titre, extrait, date
- Pagination des résultats
- Message explicite quand aucun résultat n'est trouvé
- Réutilise `template-parts/content-blog.php` pour l'affichage des résultats

### 12. Mentions légales & Politique de confidentialité

**Template**: `page.php` (pages WordPress standard)

**Contenu obligatoire (loi LCEN, France):**
- Identité: Virginie Le Mignon, L'atelier Kiyose
- SIRET: 49219620900026
- Adresse: Le Grand Vron, 86510 Brux
- Hébergeur: nom, adresse, contact
- Responsable de publication
- Politique de confidentialité (RGPD): données collectées, finalités, durée de conservation, droits des utilisateurs
- Politique cookies (liée au plugin Complianz)

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

## Design System

### Logo

**Fichier de référence**: `references/logo-kiyose.png`

**Caractéristiques du logo actuel (à conserver)**:
- Typographie script calligraphique élégante et chaleureuse
- Composition: "l'atelier" (minuscules) + "Kiyose" (capitale initiale)
- Couleur principale: Bordeaux foncé (`#5D0505` — couleur officielle de la palette)
- Éléments décoratifs: Courbes dorées organiques, points colorés
- Style: Chaleureux, créatif, artisanal, évoquant la créativité et l'authenticité

**Utilisation dans le thème**:
- Logo dans le header (version complète)
- Favicon (version simplifiée si nécessaire)
- Format: SVG optimisé pour le web (vectoriel + léger)
- Fallback PNG pour compatibilité
- Attribut `alt="L'atelier Kiyose"` pour accessibilité

### Palette de couleurs

**Fichier de référence**: `references/palette.jpg`

**Palette officielle**:

| Couleur | Code Hex | Usage prévu | Nom |
|---------|----------|-------------|-----|
| ![#D7A4A4](https://img.shields.io/badge/-D7A4A4-D7A4A4?style=flat-square) | `#D7A4A4` | Couleur primaire - Accents, boutons, liens | Rose poudré |
| ![#C9ABA7](https://img.shields.io/badge/-C9ABA7-C9ABA7?style=flat-square) | `#C9ABA7` | Couleur secondaire - Backgrounds sections | Taupe rosé |
| ![#E6A528](https://img.shields.io/badge/-E6A528-E6A528?style=flat-square) | `#E6A528` | Accent or/kintsugi - Éléments décoratifs | Or miel |
| ![#F4C975](https://img.shields.io/badge/-F4C975-F4C975?style=flat-square) | `#F4C975` | Accent doré clair - Dégradés, highlights | Jaune doré |
| ![#5D0505](https://img.shields.io/badge/-5D0505-5D0505?style=flat-square) | `#5D0505` | Texte fort, logo, titres importants | Bordeaux foncé |
| ![#EFE5E4](https://img.shields.io/badge/-EFE5E4-EFE5E4?style=flat-square) | `#EFE5E4` | Couleur de fond - Background principal | Beige clair |

> **Note** : L'image `references/palette.jpg` contient une inversion entre les cercles 4 et 5 (les codes hex affichés sous les cercles sont échangés par rapport aux couleurs visibles). Les codes ci-dessus reflètent les couleurs réelles.

**Variables CSS**:
```css
:root {
  /* Couleurs principales */
  --color-primary: #D7A4A4;          /* Rose poudré */
  --color-secondary: #C9ABA7;        /* Taupe rosé */
  --color-accent: #E6A528;           /* Or miel */
  --color-background: #EFE5E4;       /* Beige clair */
  --color-burgundy: #5D0505;         /* Bordeaux foncé (logo, titres) */

  /* Couleurs fonctionnelles */
  --color-text: #333333;             /* Texte principal */
  --color-text-light: #666666;       /* Texte secondaire */
  --color-border: #C9ABA7;           /* Bordures */

  /* Or Kintsugi */
  --color-gold: #E6A528;             /* Or principal */
  --color-gold-light: #F4C975;       /* Jaune doré clair */
  --color-gold-transparent: rgba(230, 165, 40, 0.3); /* Or transparent (overlays) */
}
```

**Exigences accessibilité**:
- ✅ Tous les textes doivent avoir contraste ≥ 4.5:1 sur leur fond
- ✅ Boutons et éléments interactifs ≥ 3:1
- ✅ États focus visibles avec contraste suffisant
- ⚠️ **CRITIQUE**: Valider tous les contrastes avec [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/) avant implémentation
- ⚠️ Si certaines couleurs ne passent pas les tests de contraste, prévoir des ajustements (assombrir/éclaircir)

**Actions de validation requises**:
1. Tester tous les contrastes texte/fond avec les couleurs officielles
2. Ajuster si nécessaire pour conformité WCAG 2.2 AA
3. Créer des variantes (hover, focus, disabled) pour les états interactifs

### Identité visuelle Kintsugi

**Philosophie de design:**
L'identité visuelle s'inspire du Kintsugi japonais - l'art de réparer les céramiques avec de la poudre d'or. Cette métaphore guide le design: les imperfections et fêlures laissent passer la lumière, transformant les blessures en beauté.

**Éléments visuels Kintsugi:**

**1. Lignes dorées subtiles**
- Séparateurs de sections inspirés des fissures réparées à l'or
- Couleur: Or miel (`#E6A528`) - couleur officielle de la palette
- Épaisseur: 1-2px, style: irrégulier/organique (pas de lignes droites parfaites)
- Utilisation: Entre les sections de la homepage, autour des cartes de services
- Technique: SVG paths pour tracés organiques, ou CSS avec border-image

```css
.section-divider {
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent 0%,
    var(--color-gold-transparent) 20%,
    var(--color-gold) 50%,
    var(--color-gold-transparent) 80%,
    transparent 100%
  );
  position: relative;
  margin: var(--spacing-3xl) 0;
}
```

**2. Effets vitrail (overlays colorés)**
- Overlays semi-transparents évoquant "l'être humain comme un vitrail"
- Utilisation: Backgrounds de sections avec dégradés de la palette (taupe rosé, rose poudré)
- Opacité: 0.05-0.15 pour ne pas nuire à la lisibilité
- Technique: Multiple background-images avec blend-modes ou pseudo-elements ::before/::after

```css
.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background:
    radial-gradient(ellipse at 20% 30%, rgba(201, 171, 167, 0.15) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 70%, rgba(215, 164, 164, 0.15) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}
```

**3. Imagerie Kintsugi**
- Graphisme subtil: motifs de fissures dorées en arrière-plan (opacity très faible)
- Icône: Possibilité d'utiliser un symbole Kintsugi stylisé comme élément graphique récurrent

**Contraintes accessibilité:**
- Tous les éléments dorés sont purement décoratifs (pas d'information transmise uniquement par la couleur)
- Contraste suffisant maintenu pour tous les textes (≥ 4.5:1)
- Overlays et backgrounds n'interfèrent jamais avec la lisibilité du contenu

### Typographie

**Approche:**
- **Titre (serif)**: Police élégante et chaleureuse (ex: Playfair Display, Lora, Crimson Text)
- **Corps (sans-serif)**: Police lisible et moderne (ex: Inter, Open Sans, Nunito)
- **Hébergement**: Auto-hébergées dans `assets/fonts/` (conformité RGPD — pas de chargement depuis Google Fonts CDN)
- Toutes les polices doivent être optimisées (subset latin, formats woff2 + woff, preload pour les polices critiques)
- Déclaration via `@font-face` dans `main.css`, chargement via `kiyose_enqueue_styles()` dans `inc/enqueue.php`

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
- **Primary**: Fond rose poudré (#D7A4A4), texte bordeaux (#5D0505), border-radius arrondi
- **Secondary**: Outline rose poudré (#D7A4A4), fond transparent
- **States**: Hover (assombrir 10%), Focus (outline visible), Active, Disabled
- Taille minimum: 44x44px (touch target WCAG 2.2)

**Cartes (Services, Témoignages):**
- Fond blanc ou beige clair (#EFE5E4)
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

**Principes généraux:**
- Animations subtiles et rapides (200-300ms)
- Respecter `prefers-reduced-motion` (désactiver animations si activé)
- Pas d'animations automatiques qui distraient
- Transitions sur hover/focus pour feedback utilisateur

**Bulles décoratives interactives:**
- Pas d'animation automatique (conformité WCAG 2.2 AA)
- Bulles SVG apparaissent au survol/focus d'éléments interactifs
- Animation déclenchée uniquement par interaction utilisateur
- Timing: apparition fade-in 300ms, disparition fade-out 200ms
- Couleurs: palette officielle (rose poudré, taupe rosé) avec opacité 0.6-0.8
- Positionnement: ne jamais chevaucher du contenu important
- Accessible au clavier (déclenchement sur focus)

**Exemples d'implémentation:**
```css
/* Bulles désactivées par défaut */
.bubble {
  opacity: 0;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Apparition au survol de cartes de services */
.service-card:hover .bubble,
.service-card:focus-within .bubble {
  opacity: 0.7;
  transform: translateY(-5px);
}

/* Respecter prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  .bubble {
    transition: none;
    transform: none;
  }
}
```

**Autres transitions:**
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

**Tailles WordPress custom** (dans `inc/setup.php`):
- Déclarer les tailles nécessaires via `add_image_size()` (ex: hero, service-card, testimony-thumb)
- Supprimer les tailles par défaut inutiles de WordPress pour réduire l'espace disque
- Utiliser `wp_get_attachment_image()` avec les tailles custom pour le rendu

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
- Templates Events Manager custom dans `plugins/events-manager/` (relatif au thème) si nécessaire

### Newsletter (En place)

**Brevo** (anciennement Sendinblue) ✅
- Plugin déjà utilisé pour la newsletter
- Formulaire dans footer + homepage
- Champs: Email (requis), Prénom (requis), Nom (optionnel)
- Double opt-in RGPD conforme

### Formulaires Contact

**Contact Form 7** ✅
- Formulaire contact personnalisé via shortcode
- Protection anti-spam (honeypot + reCAPTCHA v3)
- Validation accessible (messages d'erreur ARIA)
- Styling custom via CSS BEM (override des classes CF7 par défaut)

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
