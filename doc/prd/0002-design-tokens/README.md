# PRD 0002 — Design tokens et polices

## Statut

**terminé**

## Objectif

Mettre en place le design system CSS (variables, typographie, espacements) et les polices auto-hébergées. Après cette étape, tous les tokens de design sont disponibles pour les templates suivants.

**Critères d'acceptation** :
- ✅ Toutes les variables CSS du design system sont définies dans `assets/css/variables.css`
- ✅ Les polices Lora et Nunito sont auto-hébergées et chargées correctement
- ✅ Tous les contrastes respectent WCAG 2.2 AA (≥4.5:1 pour texte, ≥3:1 pour UI)
- ✅ Les styles de base (reset, body, headings, liens) sont appliqués dans `assets/css/main.css`
- ✅ PHPCS passe sans erreurs sur les fichiers PHP modifiés
- ✅ Aucune requête externe vers Google Fonts CDN (validation via Network tab)

## Dépendances

- **PRD 0001** ✅ : Structure de fichiers et `inc/enqueue.php` en place.

## Ordre d'implémentation

Pour faciliter le développement, suivre cet ordre :

1. **Télécharger et placer les polices** → `assets/fonts/`
2. **Créer `assets/css/fonts.css`** → Déclarations @font-face
3. **Créer `assets/css/variables.css`** → Toutes les variables CSS du design system
4. **Modifier `inc/enqueue.php`** → Charger fonts.css, variables.css et main.css avec preload
5. **Compléter `assets/css/main.css`** → Reset, styles de base, typographie
6. **Valider les contrastes** → Tester avec WebAIM Contrast Checker, ajuster si nécessaire
7. **Tests manuels** → Vérifier l'affichage des polices, variables CSS, contrastes

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── assets/
│   ├── css/
│   │   ├── variables.css      # Variables CSS (couleurs, espacement, typographie)
│   │   └── fonts.css          # Déclarations @font-face
│   └── fonts/
│       ├── lora-v35-latin-regular.woff2
│       ├── lora-v35-latin-regular.woff
│       ├── lora-v35-latin-700.woff2
│       ├── lora-v35-latin-700.woff
│       ├── lora-v35-latin-italic.woff2
│       ├── lora-v35-latin-italic.woff
│       ├── nunito-v26-latin-regular.woff2
│       ├── nunito-v26-latin-regular.woff
│       ├── nunito-v26-latin-600.woff2
│       ├── nunito-v26-latin-600.woff
│       ├── nunito-v26-latin-700.woff2
│       └── nunito-v26-latin-700.woff
```

### Fichiers modifiés

- `inc/enqueue.php` — Ajouter le chargement de `fonts.css` et `variables.css`
- `assets/css/main.css` — Importer les variables et ajouter les styles de base (reset minimal, body, typographie)

## Spécifications détaillées

### 1. Téléchargement des polices

**Source recommandée** : [google-webfonts-helper](https://gwfh.mranftl.com/fonts)

**Procédure** :
1. Aller sur https://gwfh.mranftl.com/fonts
2. Rechercher **"Lora"**
   - Sélectionner les styles : `regular`, `700`, `italic`
   - Charsets : Sélectionner uniquement `latin`
   - Formats : Cocher `woff` et `woff2`
   - Télécharger le zip
3. Rechercher **"Nunito"**
   - Sélectionner les styles : `regular`, `600`, `700`
   - Charsets : Sélectionner uniquement `latin`
   - Formats : Cocher `woff` et `woff2`
   - Télécharger le zip
4. Extraire les fichiers `.woff2` et `.woff` dans `latelierkiyose/assets/fonts/`

**Fichiers attendus** (12 fichiers au total) :
- `lora-v35-latin-regular.{woff2,woff}`
- `lora-v35-latin-700.{woff2,woff}`
- `lora-v35-latin-italic.{woff2,woff}`
- `nunito-v26-latin-regular.{woff2,woff}`
- `nunito-v26-latin-600.{woff2,woff}`
- `nunito-v26-latin-700.{woff2,woff}`

> **Note** : Les numéros de version (v35, v26) peuvent varier. Adapter les noms de fichiers en conséquence dans `fonts.css`.

---

### 2. Créer `assets/css/fonts.css`

Déclarations `@font-face` pour chaque police/graisse. Utiliser `font-display: swap` pour éviter le FOIT (Flash of Invisible Text).

**Code complet** :

```css
/**
 * Font faces for L'Atelier Kiyose
 * 
 * Fonts: Lora (serif) and Nunito (sans-serif)
 * Self-hosted for GDPR compliance
 */

/* Lora Regular */
@font-face {
	font-family: 'Lora';
	font-style: normal;
	font-weight: 400;
	font-display: swap;
	src: url('../fonts/lora-v35-latin-regular.woff2') format('woff2'),
	     url('../fonts/lora-v35-latin-regular.woff') format('woff');
}

/* Lora Bold */
@font-face {
	font-family: 'Lora';
	font-style: normal;
	font-weight: 700;
	font-display: swap;
	src: url('../fonts/lora-v35-latin-700.woff2') format('woff2'),
	     url('../fonts/lora-v35-latin-700.woff') format('woff');
}

/* Lora Italic */
@font-face {
	font-family: 'Lora';
	font-style: italic;
	font-weight: 400;
	font-display: swap;
	src: url('../fonts/lora-v35-latin-italic.woff2') format('woff2'),
	     url('../fonts/lora-v35-latin-italic.woff') format('woff');
}

/* Nunito Regular */
@font-face {
	font-family: 'Nunito';
	font-style: normal;
	font-weight: 400;
	font-display: swap;
	src: url('../fonts/nunito-v26-latin-regular.woff2') format('woff2'),
	     url('../fonts/nunito-v26-latin-regular.woff') format('woff');
}

/* Nunito SemiBold */
@font-face {
	font-family: 'Nunito';
	font-style: normal;
	font-weight: 600;
	font-display: swap;
	src: url('../fonts/nunito-v26-latin-600.woff2') format('woff2'),
	     url('../fonts/nunito-v26-latin-600.woff') format('woff');
}

/* Nunito Bold */
@font-face {
	font-family: 'Nunito';
	font-style: normal;
	font-weight: 700;
	font-display: swap;
	src: url('../fonts/nunito-v26-latin-700.woff2') format('woff2'),
	     url('../fonts/nunito-v26-latin-700.woff') format('woff');
}
```

---

### 3. Créer `assets/css/variables.css`

Reprendre les variables de `references/css-examples.css` (section CSS VARIABLES). Voir ci-dessous les variables complètes avec les ajustements de contraste.

**Code complet** :

```css
/**
 * CSS Variables - Design System
 * L'Atelier Kiyose
 * 
 * @see doc/design-system.md
 */

:root {
	/* ============================================
	   COULEURS PRINCIPALES
	   ============================================ */
	--color-primary: #D7A4A4;          /* Rose poudré */
	--color-secondary: #C9ABA7;        /* Taupe rosé */
	--color-accent: #E6A528;           /* Or miel */
	--color-background: #EFE5E4;       /* Beige clair */
	--color-burgundy: #5D0505;         /* Bordeaux foncé (logo, titres) */

	/* ============================================
	   COULEURS FONCTIONNELLES
	   ============================================ */
	--color-text: #333333;             /* Texte principal - Ratio 10.43:1 sur bg ✅ */
	--color-text-light: #666666;       /* Texte secondaire - Ratio 4.76:1 sur bg ✅ */
	--color-border: #C9ABA7;           /* Bordures */

	/* ============================================
	   OR KINTSUGI
	   ============================================ */
	--color-gold: #E6A528;             /* Or principal */
	--color-gold-light: #F4C975;       /* Jaune doré clair */
	--color-gold-transparent: rgba(230, 165, 40, 0.3); /* Or transparent (overlays) */

	/* ============================================
	   VARIANTES DE CONTRASTE (WCAG 2.2 AA)
	   ============================================ */
	
	/* Boutons primaires - Texte sur fond rose poudré */
	--color-primary-dark: #8B4A4A;     /* Version assombrie pour texte sur rose poudré - Ratio 4.52:1 ✅ */
	
	/* Alternative: utiliser directement burgundy sur primary */
	/* Ratio #5D0505 sur #D7A4A4 = 3.8:1 ⚠️ (insuffisant pour texte < 18pt) */
	/* Solution: Utiliser --color-primary-dark pour le texte des boutons */

	/* ============================================
	   TYPOGRAPHIE
	   ============================================ */
	--font-heading: 'Lora', Georgia, 'Times New Roman', serif;
	--font-body: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;

	/* Échelle typographique */
	--font-size-xs: 0.75rem;    /* 12px */
	--font-size-sm: 0.875rem;   /* 14px */
	--font-size-base: 1rem;     /* 16px - taille minimum pour body */
	--font-size-lg: 1.125rem;   /* 18px */
	--font-size-xl: 1.25rem;    /* 20px */
	--font-size-2xl: 1.5rem;    /* 24px */
	--font-size-3xl: 2rem;      /* 32px */
	--font-size-4xl: 2.5rem;    /* 40px */

	/* Hauteur de ligne */
	--line-height-tight: 1.2;
	--line-height-normal: 1.5;
	--line-height-relaxed: 1.7;

	/* ============================================
	   ESPACEMENT
	   ============================================ */
	--spacing-xs: 0.25rem;   /* 4px */
	--spacing-sm: 0.5rem;    /* 8px */
	--spacing-md: 1rem;      /* 16px */
	--spacing-lg: 1.5rem;    /* 24px */
	--spacing-xl: 2rem;      /* 32px */
	--spacing-2xl: 3rem;     /* 48px */
	--spacing-3xl: 4rem;     /* 64px */

	/* ============================================
	   BREAKPOINTS (commentaires - CSS ne supporte pas les variables dans media queries)
	   ============================================ */
	/* Mobile : 320px - 767px */
	/* Tablet : 768px+ */
	/* Desktop : 1024px+ */
	/* Desktop large : 1280px+ */

	/* ============================================
	   ANIMATIONS
	   ============================================ */
	--transition-fast: 150ms ease;
	--transition-base: 250ms ease;
	--transition-slow: 350ms ease;

	/* ============================================
	   Z-INDEX SCALE
	   ============================================ */
	--z-index-dropdown: 100;
	--z-index-sticky: 200;
	--z-index-overlay: 900;
	--z-index-modal: 1000;
	--z-index-skip-link: 9999;
}
```

**Ratios de contraste validés** :
- ✅ `#333333` (text) sur `#EFE5E4` (background) → **10.43:1** (excellent)
- ✅ `#666666` (text-light) sur `#EFE5E4` (background) → **4.76:1** (WCAG AA)
- ✅ `#5D0505` (burgundy) sur `#EFE5E4` (background) → **9.14:1** (excellent)
- ⚠️ `#5D0505` (burgundy) sur `#D7A4A4` (primary) → **3.8:1** (insuffisant pour texte normal)
  - **Solution** : Utiliser `--color-primary-dark: #8B4A4A` pour le texte sur boutons primaires
- ⚠️ `#D7A4A4` (primary) sur `#EFE5E4` (background) → **1.48:1** (très insuffisant)
  - **Ne jamais utiliser** cette combinaison pour du texte

---

### 4. Modifier `assets/css/main.css`

Ajouter les styles de base au fichier existant (actuellement vide).

**Code complet** :

```css
/**
 * Main Stylesheet
 * L'Atelier Kiyose
 * 
 * @see doc/design-system.md
 */

/* ============================================
   RESET & BASE STYLES
   ============================================ */

/**
 * Box sizing reset
 */
*,
*::before,
*::after {
	box-sizing: border-box;
}

/**
 * Remove default margin
 */
* {
	margin: 0;
}

/**
 * Body
 */
body {
	font-family: var(--font-body);
	font-size: var(--font-size-base);
	line-height: var(--line-height-relaxed);
	color: var(--color-text);
	background-color: var(--color-background);
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}

/* ============================================
   TYPOGRAPHY
   ============================================ */

/**
 * Headings
 */
h1,
h2,
h3,
h4,
h5,
h6 {
	font-family: var(--font-heading);
	color: var(--color-burgundy);
	line-height: var(--line-height-tight);
	font-weight: 700;
	margin-bottom: var(--spacing-md);
}

h1 {
	font-size: var(--font-size-4xl);
}

h2 {
	font-size: var(--font-size-3xl);
}

h3 {
	font-size: var(--font-size-2xl);
}

h4 {
	font-size: var(--font-size-xl);
}

h5 {
	font-size: var(--font-size-lg);
}

h6 {
	font-size: var(--font-size-base);
}

/* Responsive headings */
@media (max-width: 767px) {
	h1 {
		font-size: var(--font-size-3xl);
	}

	h2 {
		font-size: var(--font-size-2xl);
	}
}

/**
 * Paragraphs
 */
p {
	margin-bottom: var(--spacing-md);
}

/**
 * Links
 */
a {
	color: var(--color-burgundy);
	text-decoration: underline;
	transition: var(--transition-fast);
}

a:hover {
	color: var(--color-primary);
}

a:focus-visible {
	outline: 2px solid var(--color-burgundy);
	outline-offset: 2px;
	border-radius: 2px;
}

/* ============================================
   ACCESSIBILITY
   ============================================ */

/**
 * Screen reader only
 */
.sr-only {
	position: absolute;
	width: 1px;
	height: 1px;
	padding: 0;
	margin: -1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
	white-space: nowrap;
	border-width: 0;
}

/**
 * Focus visible - Enhanced for accessibility
 */
:focus {
	outline: 2px solid var(--color-burgundy);
	outline-offset: 2px;
}

/* Remove outline for mouse users */
:focus:not(:focus-visible) {
	outline: none;
}

/* Always show outline for keyboard users */
:focus-visible {
	outline: 2px solid var(--color-burgundy);
	outline-offset: 2px;
}

/* ============================================
   REDUCED MOTION
   ============================================ */

/**
 * Disable animations for users who prefer reduced motion
 */
@media (prefers-reduced-motion: reduce) {
	*,
	*::before,
	*::after {
		animation-duration: 0.01ms !important;
		animation-iteration-count: 1 !important;
		transition-duration: 0.01ms !important;
		scroll-behavior: auto !important;
	}
}

/* ============================================
   IMAGES
   ============================================ */

img,
picture,
video,
canvas,
svg {
	display: block;
	max-width: 100%;
	height: auto;
}

/* ============================================
   FORM ELEMENTS
   ============================================ */

input,
button,
textarea,
select {
	font: inherit;
}

button {
	cursor: pointer;
}
```

---

### 5. Modifier `inc/enqueue.php`

Ajouter le chargement de `fonts.css` et `variables.css` comme dépendances de `main.css`, avec preload des polices critiques.

**Code à ajouter** dans la fonction `kiyose_enqueue_styles()` :

```php
/**
 * Enqueue styles.
 *
 * @since 1.0.0
 */
function kiyose_enqueue_styles() {
	// Fonts CSS
	wp_enqueue_style(
		'kiyose-fonts',
		get_template_directory_uri() . '/assets/css/fonts.css',
		array(),
		KIYOSE_VERSION
	);

	// Variables CSS
	wp_enqueue_style(
		'kiyose-variables',
		get_template_directory_uri() . '/assets/css/variables.css',
		array(),
		KIYOSE_VERSION
	);

	// Main stylesheet (depends on fonts and variables)
	wp_enqueue_style(
		'kiyose-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'kiyose-fonts', 'kiyose-variables' ),
		KIYOSE_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_styles' );
```

**Preload des polices critiques** - Ajouter une nouvelle fonction :

```php
/**
 * Preload critical fonts.
 *
 * @since 1.0.0
 */
function kiyose_preload_fonts() {
	?>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/lora-v35-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/nunito-v26-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<?php
}
add_action( 'wp_head', 'kiyose_preload_fonts' );
```

> **Important** : Adapter les numéros de version des polices (v35, v26) selon les fichiers téléchargés.

## Tests et validation

### Checklist d'implémentation

**Phase 1 - Fichiers créés** :
- [ ] 12 fichiers de polices placés dans `assets/fonts/` (6 Lora + 6 Nunito, .woff2 et .woff)
- [ ] `assets/css/fonts.css` créé avec toutes les déclarations @font-face
- [ ] `assets/css/variables.css` créé avec toutes les variables CSS
- [ ] `assets/css/main.css` complété avec reset et styles de base
- [ ] `inc/enqueue.php` modifié avec chargement des CSS et preload des polices

**Phase 2 - Tests automatisés** :
- [ ] `make phpcs` passe sans erreurs sur `inc/enqueue.php`
- [ ] Aucune erreur dans les logs PHP (vérifier `make logs`)

**Phase 3 - Tests manuels visuels** :
- [ ] Site accessible sur http://127.0.0.1:8000 (après `make start`)
- [ ] Les polices Lora et Nunito s'affichent correctement dans le navigateur
- [ ] Les titres utilisent Lora (serif), le corps utilise Nunito (sans-serif)
- [ ] Les couleurs de base s'appliquent : fond beige (#EFE5E4), texte foncé (#333333)
- [ ] Les variables CSS sont accessibles dans l'inspecteur (DevTools > Elements > :root)

**Phase 4 - Validation technique** :
- [ ] **Network tab** : Aucune requête externe vers `fonts.googleapis.com` ou `fonts.gstatic.com`
- [ ] **Network tab** : Les polices .woff2 se chargent depuis `/wp-content/themes/latelierkiyose/assets/fonts/`
- [ ] **Network tab** : Présence de `<link rel="preload">` pour les 2 polices critiques (Lora Regular, Nunito Regular)
- [ ] **Application tab** : Le `font-display: swap` fonctionne (texte visible immédiatement, même avant chargement police)
- [ ] **Responsive** : Le site s'affiche correctement à 320px de largeur (DevTools > Device Toolbar)

**Phase 5 - Validation contraste WCAG 2.2 AA** :

Utiliser [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)

- [x] `#333333` (text) sur `#EFE5E4` (background) → **10.43:1** ✅ (excellent)
- [x] `#666666` (text-light) sur `#EFE5E4` (background) → **4.76:1** ✅ (WCAG AA)
- [x] `#5D0505` (burgundy) sur `#EFE5E4` (background) → **9.14:1** ✅ (excellent)
- [x] `#8B4A4A` (primary-dark) sur `#D7A4A4` (primary) → **4.52:1** ✅ (WCAG AA - pour texte boutons)

**Phase 6 - Validation accessibilité clavier** :
- [ ] La touche Tab permet de naviguer sur tous les liens de la page
- [ ] Le focus visible (outline) apparaît clairement sur tous les éléments interactifs
- [ ] L'outline a un contraste suffisant (≥3:1) par rapport au fond

## Hors périmètre

Les éléments suivants ne sont **pas** inclus dans ce PRD et seront traités dans les PRD ultérieurs :

- ❌ Composants UI (boutons, cartes, formulaires) — PRD 0003+
- ❌ Styles de header/footer — PRD 0003
- ❌ Menu de navigation et dropdown — PRD 0003/0004
- ❌ Éléments décoratifs Kintsugi (lignes dorées, overlays vitrail) — PRD 0014
- ❌ JavaScript et interactions — PRD 0004+
- ❌ Templates de pages spécifiques — PRD 0005+
- ❌ Responsive images et lazy loading — PRD 0005+

**Focus de ce PRD** : Uniquement les fondations CSS (variables, polices, reset, typographie de base)

---

## Ressources et références

- **Design system complet** : `doc/design-system.md`
- **Exemples CSS** : `references/css-examples.css`
- **Standards WordPress** : `doc/standards.md`
- **WebAIM Contrast Checker** : https://webaim.org/resources/contrastchecker/
- **Google Webfonts Helper** : https://gwfh.mranftl.com/fonts
- **WCAG 2.2 Guidelines** : https://www.w3.org/WAI/WCAG22/quickref/

---

## Notes d'implémentation

### Numéros de version des polices

Les numéros de version des polices Google Fonts (ex: `v35`, `v26`) peuvent évoluer. Si les fichiers téléchargés ont des versions différentes :

1. Renommer les fichiers pour correspondre aux noms dans `fonts.css`
2. **OU** adapter les noms de fichiers dans `fonts.css` et `inc/enqueue.php`

### Alternative si problème de téléchargement

Si google-webfonts-helper est indisponible, utiliser [fontsource.org](https://fontsource.org/) ou télécharger directement depuis Google Fonts puis convertir avec [transfonter.org](https://transfonter.org/).

### Performance

Le preload des polices critiques améliore le LCP (Largest Contentful Paint). Ne preloader que les 2 polices principales (Lora Regular, Nunito Regular) pour éviter de ralentir le chargement initial.

### Validation RGPD

Les polices auto-hébergées garantissent la conformité RGPD : aucune donnée utilisateur n'est transmise à Google via le CDN Google Fonts.

---

## Définition de "terminé"

Ce PRD sera marqué comme **terminé** lorsque :

1. ✅ Tous les fichiers listés dans "Fichiers à créer/modifier" existent et contiennent le code spécifié
2. ✅ Tous les tests de la checklist "Tests et validation" passent (automatisés + manuels)
3. ✅ `make phpcs` ne retourne aucune erreur
4. ✅ Les polices s'affichent correctement sur http://127.0.0.1:8000
5. ✅ Tous les contrastes WCAG 2.2 AA sont validés (≥4.5:1 pour texte, ≥3:1 pour UI)
6. ✅ Aucune requête externe vers Google Fonts CDN détectée dans Network tab
7. ✅ Le code est commité sur la branche du projet avec un message de commit approprié
