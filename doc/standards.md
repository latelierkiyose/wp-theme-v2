# Standards & Contraintes

## WordPress Coding Standards (WPCS)

### Obligatoire pour tout le code PHP

**Référence**: [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)

### Indentation et formatage
- **Indentation**: Tabs (jamais d'espaces)
- **Lignes**: Maximum 120 caractères recommandé
- **Accolades**: Style BSD/Allman (accolade ouvrante sur nouvelle ligne pour fonctions/classes)

### Nommage
- **Fonctions**: `kiyose_function_name()` (snake_case avec préfixe)
- **Hooks**: `kiyose_hook_name` (snake_case avec préfixe)
- **Classes**: `Kiyose_Class_Name` (PascalCase avec préfixe)
- **Constantes**: `KIYOSE_CONSTANT_NAME` (UPPERCASE avec préfixe)
- **Variables**: `$variable_name` (snake_case)

### Documentation
```php
/**
 * Brève description.
 *
 * Description plus longue si nécessaire.
 *
 * @since 1.0.0
 *
 * @param string $param1 Description.
 * @param int    $param2 Description.
 * @return bool Description.
 */
function kiyose_function_name( $param1, $param2 ) {
	// Code
}
```

### Validation

**Outil**: PHP_CodeSniffer avec WordPress rulesets

```bash
# Installation
composer require --dev squizlabs/php_codesniffer
composer require --dev wp-coding-standards/wpcs

# Vérification
./vendor/bin/phpcs --standard=WordPress latelierkiyose/

# Auto-fix (quand possible)
./vendor/bin/phpcbf --standard=WordPress latelierkiyose/
```

## Sécurité

### Principe fondamental
**Toujours supposer que les données sont non sûres.**

### Sanitization (entrées)

**Sanitize toutes les entrées utilisateur**:

```php
// Texte simple
$clean_text = sanitize_text_field( $_POST['field'] );

// Texte multiligne
$clean_textarea = sanitize_textarea_field( $_POST['textarea'] );

// Email
$clean_email = sanitize_email( $_POST['email'] );

// URL
$clean_url = esc_url_raw( $_POST['url'] );

// Clés de tableaux
$clean_key = sanitize_key( $_POST['key'] );

// HTML (avec Kses)
$allowed_html = array(
	'a' => array( 'href' => array(), 'title' => array() ),
	'b' => array(),
	'strong' => array(),
);
$clean_html = wp_kses( $_POST['content'], $allowed_html );
```

### Escaping (sorties)

**Toujours échapper les sorties**:

```php
// Texte HTML
echo esc_html( $text );

// Attributs HTML
echo '<div class="' . esc_attr( $class ) . '">';

// URL
echo '<a href="' . esc_url( $url ) . '">';

// JavaScript
echo '<script>var data = ' . wp_json_encode( $data ) . ';</script>';

// Texte dans attributs i18n
esc_attr_e( 'Text', 'kiyose' );
esc_html_e( 'Text', 'kiyose' );
```

### Nonces

**Utiliser pour tous les formulaires et actions**:

```php
// Création (dans le formulaire)
wp_nonce_field( 'kiyose_action_name', 'kiyose_nonce_field' );

// Vérification (traitement)
if ( ! isset( $_POST['kiyose_nonce_field'] ) || 
     ! wp_verify_nonce( $_POST['kiyose_nonce_field'], 'kiyose_action_name' ) ) {
	wp_die( __( 'Échec de la vérification de sécurité', 'kiyose' ) );
}
```

### Requêtes base de données

**Toujours utiliser prepared statements**:

```php
global $wpdb;

// Préparation de requête
$results = $wpdb->get_results( $wpdb->prepare(
	"SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
	'kiyose_testimony',
	'publish'
) );

// Types de placeholders:
// %s - chaîne de caractères (string)
// %d - entier (integer)
// %f - nombre décimal (float)
```

### Capabilities et permissions

**Vérifier les permissions**:

```php
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'Vous n\'avez pas la permission d\'accéder à cette page.', 'kiyose' ) );
}
```

## Accessibilité WCAG 2.2 AA (Priorité absolue)

### Contraste de couleurs

**Exigences**:
- Texte normal: minimum 4.5:1
- Texte large (18pt+/14pt+ gras): minimum 3:1
- Composants UI et graphiques: minimum 3:1

**Validation**:
- Tous les contrastes doivent être testés avec [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)
- Si une combinaison échoue, ajuster les couleurs (assombrir/éclaircir)

### Navigation clavier

**Exigences**:
- Tous les éléments interactifs accessibles au clavier (Tab, Enter, Esc, flèches)
- Focus visible sur tous les éléments (outline ou alternative visuelle)
- Ordre de tabulation logique (correspond à l'ordre visuel)
- Pas de piège clavier (focus trap uniquement dans modals avec Esc pour sortir)

**CSS pour focus visible**:
```css
/* Désactiver outline par défaut seulement si alternative fournie */
:focus {
	outline: 2px solid var(--color-primary);
	outline-offset: 2px;
}

/* Supprimer outline seulement pour souris */
:focus:not(:focus-visible) {
	outline: none;
}

/* Focus clavier toujours visible */
:focus-visible {
	outline: 2px solid var(--color-primary);
	outline-offset: 2px;
}
```

### Landmarks ARIA

**Structure sémantique obligatoire**:

```html
<header role="banner">
	<nav role="navigation" aria-label="Menu principal">
		<!-- Navigation -->
	</nav>
</header>

<main role="main">
	<!-- Contenu principal -->
</main>

<aside role="complementary" aria-label="Actualités">
	<!-- Contenu complémentaire -->
</aside>

<footer role="contentinfo">
	<!-- Pied de page -->
</footer>
```

### Images

**Textes alternatifs**:
- Images porteuses d'information: `alt` descriptif
- Images décoratives: `alt=""` (vide, pas absent)
- Logos: `alt="Nom de l'entreprise"`

```php
// Image WordPress
echo wp_get_attachment_image(
	$image_id,
	'full',
	false,
	array( 'alt' => esc_attr( $alt_text ) )
);

// Image HTML
<img src="<?php echo esc_url( $image_url ); ?>" 
     alt="<?php echo esc_attr( $alt_text ); ?>" 
     loading="lazy">
```

### Formulaires

**Labels explicites**:
```html
<label for="email-field">Adresse email (requis)</label>
<input type="email" id="email-field" name="email" required 
       aria-required="true" aria-describedby="email-help">
<span id="email-help" class="help-text">
	Format: nom@exemple.com
</span>
```

**Messages d'erreur**:
```html
<input type="email" id="email-field" name="email" 
       aria-invalid="true" aria-describedby="email-error">
<span id="email-error" class="error-message" role="alert">
	Veuillez entrer une adresse email valide
</span>
```

### Headings hiérarchiques

**Règles**:
- Un seul `<h1>` par page
- Hiérarchie logique sans sauter de niveaux (`<h1>` → `<h2>` → `<h3>`, jamais `<h1>` → `<h3>`)
- Headings décrivent le contenu qui suit

### Links

**Textes descriptifs**:
```html
<!-- ❌ Mauvais -->
<a href="/services">Cliquez ici</a>

<!-- ✅ Bon -->
<a href="/services">Découvrir nos services</a>

<!-- ✅ Bon avec contexte -->
<a href="/services" aria-label="Découvrir nos services d'art-thérapie">
	En savoir plus
</a>
```

### Skip links

**Obligatoire pour navigation rapide**:

```html
<!-- Début de header.php -->
<a href="#main-content" class="skip-link">
	Aller au contenu principal
</a>
```

```css
.skip-link {
	position: absolute;
	top: -40px;
	left: 0;
	background: var(--color-primary);
	color: white;
	padding: 8px;
	z-index: 100;
}

.skip-link:focus {
	top: 0;
}
```

### Animations et mouvement

**Respecter prefers-reduced-motion**:

```css
/* Animations par défaut */
@media (prefers-reduced-motion: no-preference) {
	.element {
		transition: transform 0.3s ease;
	}
}

/* Désactiver si réduit */
@media (prefers-reduced-motion: reduce) {
	.element {
		transition: none;
	}
}
```

## Mobile-First & Responsive

### Approche

**Concevoir d'abord pour mobile (320px+)**:
```css
/* Mobile par défaut */
.container {
	padding: 1rem;
}

/* Tablet */
@media (min-width: 768px) {
	.container {
		padding: 2rem;
	}
}

/* Desktop */
@media (min-width: 1024px) {
	.container {
		padding: 3rem;
		max-width: 1200px;
		margin: 0 auto;
	}
}
```

### Breakpoints

**Breakpoints suggérés**:
- Mobile: 320px - 767px
- Tablet: 768px - 1023px
- Desktop: 1024px+

### Touch targets (WCAG 2.2)

**Taille minimum: 44x44px**:
```css
.button,
.link-important {
	min-width: 44px;
	min-height: 44px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
}
```

### Images responsives

**Toujours utiliser srcset et sizes**:
```html
<img 
	src="image-800w.webp"
	srcset="image-400w.webp 400w, 
	        image-800w.webp 800w, 
	        image-1200w.webp 1200w"
	sizes="(max-width: 768px) 100vw, 50vw"
	alt="Description"
	loading="lazy">
```

### Pas de scroll horizontal

```css
/* Prévenir overflow */
* {
	max-width: 100%;
}

img,
video {
	height: auto;
}
```

## Performance

### Objectifs Core Web Vitals

- **LCP (Largest Contentful Paint)**: < 2.5s
- **INP (Interaction to Next Paint)**: < 200ms
- **CLS (Cumulative Layout Shift)**: < 0.1

### Optimisations CSS

**Minification en production**:
- Enlever commentaires et espaces
- Utiliser outils comme cssnano ou build tools

**CSS critique inline**:
- Inline CSS above-the-fold dans `<head>`
- Charger le reste en différé

### Optimisations JavaScript

**Pas de jQuery** (sauf si absolument nécessaire):
- Vanilla JS suffit pour la plupart des cas
- Bibliothèques modernes si vraiment nécessaire

**Chargement différé**:
```php
// Scripts non-critiques
wp_enqueue_script( 'kiyose-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0', true );

// defer ou async selon besoin
add_filter( 'script_loader_tag', 'kiyose_defer_scripts', 10, 2 );
function kiyose_defer_scripts( $tag, $handle ) {
	if ( 'kiyose-main' === $handle ) {
		return str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
```

### Images

**Formats**:
- WebP avec fallback JPG/PNG
- SVG pour icônes et logos

**Optimisation**:
- Compression avant upload (TinyPNG, Squoosh)
- Lazy loading: `loading="lazy"` pour images below-the-fold
- Sizes appropriées (pas de 4000px pour affichage 400px)

### Cache

**Headers cache navigateur**:
- Assets statiques: cache long (1 an)
- HTML: cache court ou validation

## Tests

### Tests unitaires PHP

**Outil**: PHPUnit

**Couverture**:
- Fonctions critiques dans `inc/`
- Custom Post Types
- Sanitization/validation functions

**Exemple**:
```php
class Test_Kiyose_Setup extends WP_UnitTestCase {
	public function test_theme_support_registered() {
		$this->assertTrue( current_theme_supports( 'title-tag' ) );
		$this->assertTrue( current_theme_supports( 'post-thumbnails' ) );
	}
}
```

### Tests d'accessibilité

**Automatisés**:
- axe DevTools (extension navigateur)
- pa11y (CLI)
- Lighthouse accessibility audit

**Manuels**:
- Navigation clavier complète (Tab, Enter, Esc, flèches)
- Tests lecteur d'écran (NVDA Windows / VoiceOver macOS)
- Zoom 200% sans perte de fonctionnalité

### Tests navigateurs

**Navigateurs à tester**:
- Chrome (dernière version)
- Firefox (dernière version)
- Safari (dernière version)
- iOS Safari (iOS récent)
- Chrome Android (version récente)

### Tests performance

**Outils**:
- Lighthouse (Chrome DevTools)
- PageSpeed Insights (https://pagespeed.web.dev/)
- WebPageTest (https://www.webpagetest.org/)

**Métriques à surveiller**:
- Core Web Vitals (LCP, INP, CLS)
- Time to First Byte (TTFB)
- Total Blocking Time (TBT)

### Validation HTML

**W3C Validator**: https://validator.w3.org/

Valider toutes les pages principales pour HTML conforme.

## Outils de développement

### Recommandés

**PHP**:
- PHP_CodeSniffer (WPCS)
- PHPUnit
- Composer (gestion dépendances)

**Accessibilité**:
- axe DevTools
- WAVE browser extension
- Accessibility Insights

**Performance**:
- Lighthouse
- WebPageTest

**Validation**:
- W3C HTML Validator
- CSS Validator
