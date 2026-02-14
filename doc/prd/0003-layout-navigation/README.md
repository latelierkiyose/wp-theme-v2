# PRD 0003 — Layout et navigation desktop

## Statut

**brouillon**

## Objectif

Créer le layout global du site (header avec navigation, footer) et les composants d'accessibilité de base (skip link, landmarks ARIA). Après cette étape, toutes les pages ont un header/footer cohérent et une navigation desktop fonctionnelle.

## Dépendances

- **PRD 0001** : Structure de fichiers en place.
- **PRD 0002** : Variables CSS et polices disponibles.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── header.php                     # En-tête du site
├── footer.php                     # Pied de page
├── inc/
│   └── accessibility.php          # Fonctions d'accessibilité
└── assets/
    └── css/
        └── components/
            ├── header.css         # Styles du header
            ├── footer.css         # Styles du footer
            └── navigation.css     # Styles navigation desktop
```

### Fichiers modifiés

- `functions.php` — Ajouter le `require_once` de `inc/accessibility.php`
- `inc/enqueue.php` — Charger les CSS de composants
- `assets/css/main.css` — Ajouter les styles de layout global (container, main)

## Spécifications détaillées

### `header.php`

Structure HTML :

```
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="skip-link">
    Aller au contenu principal
</a>

<header class="site-header" role="banner">
    <div class="site-header__container">
        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" aria-label="L'Atelier Kiyose - Accueil">
            <!-- Logo image ou custom_logo -->
        </a>

        <!-- Navigation desktop -->
        <nav class="main-nav" role="navigation" aria-label="Menu principal">
            <!-- wp_nav_menu() avec theme_location 'primary' -->
        </nav>

        <!-- Bouton hamburger mobile (masqué en desktop) -->
        <!-- Voir PRD 0004 -->
    </div>
</header>

<main id="main-content" role="main">
```

**Points clés** :
- Le skip link est le premier élément focusable après `<body>`
- Le logo renvoie à l'accueil avec un `aria-label` explicite
- Le `<main>` s'ouvre dans `header.php` (et se ferme dans `footer.php`)
- Utiliser `<?php wp_nav_menu() ?>` avec les paramètres décrits dans `doc/architecture.md`

### Navigation desktop (wp_nav_menu)

Configuration :
- `theme_location` : `primary`
- `menu_class` : `main-nav__list`
- `container` : `false` (le `<nav>` est dans le markup PHP)
- `depth` : 2 (sous-menus supportés)
- `fallback_cb` : `false`

**Sous-menus (dropdown)** :
- Apparition au `:hover` et `:focus-within` du parent
- Navigation clavier : Tab entre items, Enter ouvre le sous-menu, Esc ferme, flèches haut/bas dans le sous-menu
- `aria-haspopup="true"` et `aria-expanded="false/true"` sur les items parents
- Le comportement JS des dropdowns sera géré dans `assets/js/modules/dropdown.js` (créé dans ce PRD)

### `footer.php`

Structure HTML :

```
</main><!-- #main-content -->

<footer class="site-footer" role="contentinfo">
    <div class="site-footer__container">
        <!-- Réseaux sociaux -->
        <div class="site-footer__social">
            <a href="URL_FACEBOOK" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                <!-- SVG icon -->
            </a>
            <!-- Instagram, LinkedIn -->
        </div>

        <!-- Newsletter (placeholder pour PRD 0013) -->
        <div class="site-footer__newsletter">
            <!-- Brevo shortcode sera ajouté dans PRD 0013 -->
        </div>

        <!-- Liens légaux -->
        <div class="site-footer__legal">
            <a href="/mentions-legales/">Mentions légales</a>
            <a href="/politique-de-confidentialite/">Politique de confidentialité</a>
        </div>

        <!-- Copyright + SIRET -->
        <div class="site-footer__copyright">
            <p>&copy; <?php echo date( 'Y' ); ?> L'Atelier Kiyose — SIRET 49219620900026</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
```

**Points clés** :
- Icônes réseaux sociaux en SVG inline (léger, stylable, accessible)
- Les URLs des réseaux sociaux sont dans `references/business-info.json`
- L'emplacement newsletter est un placeholder vide (rempli dans PRD 0013)
- Les liens légaux pointent vers des pages WordPress standard (à créer manuellement)

### `inc/accessibility.php`

Fonctions utilitaires :
- `kiyose_skip_link()` : Génère le HTML du skip link (si on veut le rendre configurable)
- `kiyose_social_icon_svg( $network )` : Retourne le SVG inline pour un réseau social donné (Facebook, Instagram, LinkedIn)

### Styles CSS

**`components/header.css`** :
- Header sticky ou fixe en desktop
- Logo à gauche, navigation à droite
- Responsive : menu horizontal en desktop (≥1024px)
- Focus visible sur tous les éléments interactifs

**`components/navigation.css`** :
- Items de navigation horizontaux
- Sous-menus (dropdown) : positionnement absolu, apparition au hover/focus-within
- Styles actifs (page courante) via la classe WordPress `current-menu-item`
- Transitions respectant `prefers-reduced-motion`

**`components/footer.css`** :
- Layout en colonnes (Grid ou Flexbox)
- Icônes réseaux sociaux : taille 44x44px minimum (touch target WCAG)
- Mobile : colonnes empilées

**`main.css` (ajouts)** :
- `.container` avec padding responsive et max-width 1200px
- Styles de base du layout (voir `references/css-examples.css` section RESPONSIVE)

### `assets/js/modules/dropdown.js`

Gestion accessible des sous-menus desktop :
- Ouvrir/fermer au clavier (Enter, Esc)
- Navigation par flèches dans les sous-menus
- `aria-expanded` mis à jour dynamiquement
- Fermeture quand le focus sort du sous-menu

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur tous les fichiers PHP créés/modifiés

### Tests manuels — Structure
- [ ] Toutes les pages ont un header et un footer
- [ ] Le logo est cliquable et renvoie à l'accueil
- [ ] Le menu principal affiche les items configurés dans l'admin WordPress

### Tests manuels — Accessibilité
- [ ] Skip link apparaît au Tab et fonctionne (focus saute au contenu principal)
- [ ] Landmarks ARIA présents : `banner`, `navigation`, `main`, `contentinfo`
- [ ] Navigation clavier complète : Tab entre items, Enter active les liens
- [ ] Sous-menus accessibles au clavier (Enter ouvre, Esc ferme, flèches naviguent)
- [ ] Focus visible sur tous les éléments interactifs du header et footer
- [ ] Icônes réseaux sociaux ont des `aria-label` appropriés

### Tests manuels — Responsive
- [ ] Header : menu horizontal en desktop (≥1024px)
- [ ] Header : bouton hamburger visible en mobile (<1024px), menu caché
- [ ] Footer : colonnes en desktop, empilé en mobile
- [ ] Cibles tactiles ≥ 44x44px pour icônes et liens

## Hors périmètre

- Menu mobile / hamburger interactif (PRD 0004 — le bouton est rendu mais pas fonctionnel)
- Newsletter dans le footer (PRD 0013)
- Éléments décoratifs Kintsugi (PRD 0014)
