# Architecture Technique

## Type de thème

**Thème WordPress classique** avec templates PHP (pas de Block Theme/FSE).

## Structure des fichiers

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
    │   │   ├── variables.css     # Variables CSS (couleurs, espacements, typographie)
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

## Stack technique

- **PHP**: 8.1+ (dernière version stable)
- **WordPress**: 6.7+ (dernière version)
- **CSS**: CSS natif moderne (variables CSS, Grid, Flexbox) - Pas de framework lourd
- **JavaScript**: Vanilla JS ES6+ - Bibliothèques minimales uniquement si nécessaire
- **Build tools**: Optionnel (évaluer si npm/webpack nécessaire ou CSS/JS natif suffisant)

## Templates WordPress utilisés

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

### Note sur les templates personnalisés

Les fichiers dans `templates/` ne bénéficient pas de la résolution automatique par slug WordPress. Chaque fichier doit :

1. Contenir un header `/* Template Name: Nom du template */`
2. Être assigné manuellement dans l'éditeur de page WordPress

**Workflow d'assignation**:
1. Créer le fichier template avec le header approprié
2. Dans WordPress admin, éditer la page concernée
3. Dans Page Attributes > Template, sélectionner le template
4. Publier/Mettre à jour la page

## Conventions

### Nommage
- **Préfixe PHP**: `kiyose_` pour toutes les fonctions
- **Préfixe hooks**: `kiyose_` pour les hooks custom
- **Text domain**: `kiyose` (pas d'i18n nécessaire pour ce projet)

### CSS
- **Méthodologie**: BEM (Block Element Modifier)
- **Exemples**:
  - Block: `.service-card`
  - Element: `.service-card__title`
  - Modifier: `.service-card--featured`

### Chargement modulaire

`functions.php` charge les fichiers `inc/*.php` via `require_once`:

```php
<?php
// Charger la configuration du thème
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/accessibility.php';
```

## Environnement de développement local

### Docker Compose

Le projet utilise Docker Compose pour démarrer un environnement WordPress complet.

**Commande de démarrage**:
```bash
docker compose up
```

**Accès**:
- Site WordPress: http://127.0.0.1:8000
- Interface d'administration: http://127.0.0.1:8000/wp-admin

**Notes**:
- Le thème est monté dans le container et accessible immédiatement
- Les modifications de fichiers sont reflétées en temps réel (pas de rebuild nécessaire)
- Pour arrêter: `docker compose down`

**Credentials WordPress**:
- Configurés lors de la première installation
- Vérifier `docker-compose.yaml` pour les variables d'environnement si applicable

## Custom Post Types

### Témoignages (kiyose_testimony)

**Déclaration**: `inc/custom-post-types.php`

**Champs**:
- Citation (contenu principal)
- Prénom (custom field)
- Contexte: individu/entreprise/structure (custom field ou taxonomy)

**Affichage**:
- Pas d'archive publique dédiée
- Affichés via template parts (`template-parts/content-testimony.php`)
- Queries custom dans les templates (homepage, page témoignages)

**Exemple de query**:
```php
$testimonies = new WP_Query( array(
	'post_type'      => 'kiyose_testimony',
	'posts_per_page' => 3,
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
```

## Navigation

### Menu principal

**Enregistrement**: `inc/setup.php`

```php
register_nav_menus( array(
	'primary' => __( 'Menu Principal', 'kiyose' ),
) );
```

**Affichage dans template**:
```php
wp_nav_menu( array(
	'theme_location' => 'primary',
	'menu_class'     => 'main-nav',
	'container'      => 'nav',
	'container_class' => 'main-nav-wrapper',
) );
```

**Niveaux supportés**:
- Niveau 1: Items visibles dans la barre de navigation
- Niveau 2: Dropdown au survol/focus

**Accessibilité**:
- Navigation clavier complète (Tab, Enter, Esc, flèches)
- ARIA attributes appropriés
- Focus visible sur tous les items

### Menu mobile (hamburger)

**Exigences**:
- Bouton avec `aria-expanded="false/true"` et `aria-controls="mobile-menu"`
- Focus trap quand ouvert
- Body scroll verrouillé (`overflow: hidden`)
- Fermeture: bouton close, touche Échap, ou clic overlay
- Focus retourne au bouton hamburger à la fermeture
- Transition respectant `prefers-reduced-motion`

## Assets

### Polices

**Hébergement**: Auto-hébergées dans `assets/fonts/` (conformité RGPD)

**Optimisation**:
- Subset latin uniquement
- Formats: woff2 (principal) + woff (fallback)
- Preload pour polices critiques

**Déclaration**:
```php
// inc/enqueue.php
function kiyose_enqueue_styles() {
	wp_enqueue_style( 'kiyose-fonts', get_template_directory_uri() . '/assets/css/fonts.css', array(), '1.0' );
	wp_enqueue_style( 'kiyose-main', get_template_directory_uri() . '/assets/css/main.css', array( 'kiyose-fonts' ), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_styles' );
```

### Images

**Formats**:
- WebP avec fallback JPG/PNG
- SVG pour logos et icônes

**Tailles personnalisées** (`inc/setup.php`):
```php
add_image_size( 'kiyose-hero', 1920, 800, true );
add_image_size( 'kiyose-service-card', 600, 400, true );
add_image_size( 'kiyose-testimony-thumb', 150, 150, true );
```

**Responsive images**:
```php
echo wp_get_attachment_image(
	$attachment_id,
	'kiyose-service-card',
	false,
	array(
		'loading' => 'lazy',
		'alt'     => esc_attr( $alt_text ),
	)
);
```

## Sécurité

### Sanitization et Escaping

**Toujours sanitizer les entrées**:
```php
$clean_input = sanitize_text_field( $_POST['field'] );
$clean_url = esc_url_raw( $_POST['url'] );
$clean_email = sanitize_email( $_POST['email'] );
```

**Toujours échapper les sorties**:
```php
echo esc_html( $text );
echo esc_attr( $attribute );
echo esc_url( $url );
```

### Nonces

**Création**:
```php
wp_nonce_field( 'kiyose_action_name', 'kiyose_nonce_field' );
```

**Vérification**:
```php
if ( ! isset( $_POST['kiyose_nonce_field'] ) || ! wp_verify_nonce( $_POST['kiyose_nonce_field'], 'kiyose_action_name' ) ) {
	wp_die( 'Échec de la vérification de sécurité' );
}
```

### Requêtes DB

**Toujours utiliser prepared statements**:
```php
global $wpdb;
$results = $wpdb->get_results( $wpdb->prepare(
	"SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
	'kiyose_testimony',
	'publish'
) );
```

## Hooks personnalisés

**Créer des hooks pour extensibilité**:

```php
// Dans le template
do_action( 'kiyose_before_service_card', $post_id );
// Contenu de la carte
do_action( 'kiyose_after_service_card', $post_id );
```

**Utilisation**:
```php
add_action( 'kiyose_before_service_card', 'my_custom_function' );
function my_custom_function( $post_id ) {
	// Code personnalisé
}
```
