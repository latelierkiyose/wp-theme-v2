# PRD 0032 — Shapes décoratives sur toutes les pages

## Statut

**terminé**

## Objectif

Étendre le système de shapes décoratives page-level à toutes les pages du site, avec une densité qui s'adapte automatiquement à la longueur réelle de chaque page.

## Contexte

Les shapes décoratives (splashes, blobs, brushstrokes, spirals) sont un succès visuel sur la homepage. Elles sont actuellement chargées et rendues exclusivement via `page-home.php` et le hook d'enqueue conditionnel dans `inc/enqueue.php`. Le CSS est déjà global (`kiyose-kintsugi`). Il manque :

1. Un mécanisme de configuration et de rendu universel
2. Un système de densité adaptatif (les pages varient de ~500px à ~3000px de hauteur)
3. La suppression du gate homepage sur le JS de reveal

**Architecture choisie** : configuration PHP centralisée + rendu depuis `footer.php` + densité dynamique JS.

## Dépendances

- [x] [PRD 0020 — Éléments décoratifs expressifs](../0020-decoratifs-collage/) (système de shapes)
- [x] [PRD 0026 — Extension de la surface shapes](../0026-extend-shapes-domain/) (positions content-left/right)
- [ ] [PRD 0030 — Fix `<main>` imbriqué](../0030-fix-main-nesting/) — **BLOQUANT** (fournit `position: relative` sur `.site-main` et la règle z-index)

## Spécifications

### 1. Configuration centralisée — `inc/decorative-config.php`

Nouveau fichier qui expose `kiyose_get_page_shapes()` : retourne le pool de shapes à rendre pour la page courante, ou `null` si aucune shapes ne doit être injectée.

```php
/**
 * Returns the pool of decorative shapes for the current page.
 *
 * Returns null for the homepage (shapes are hardcoded in page-home.php)
 * and for pages that should not have shapes.
 *
 * @return array<int, array<string, mixed>>|null Array of shape configs, or null.
 */
function kiyose_get_page_shapes() {
	// Homepage : shapes hardcodées dans page-home.php, ne pas dupliquer.
	if ( is_page_template( 'templates/page-home.php' ) ) {
		return null;
	}

	return kiyose_build_shapes_pool();
}
```

#### Pool universel de shapes

La fonction `kiyose_build_shapes_pool()` retourne un tableau de ~16 shapes couvrant 2% à 95% de la page :

```php
function kiyose_build_shapes_pool() {
	return array(
		// --- Margin shapes (gauche/droite alternées) ---
		array( 'type' => 'splash',      'color' => '--kiyose-color-accent',       'opacity' => 0.30, 'size' => 'md', 'position' => 'margin-left',  'top' => '4%',  'rotation' => 12  ),
		array( 'type' => 'blob',        'color' => '--kiyose-color-warm-yellow',  'opacity' => 0.35, 'size' => 'lg', 'position' => 'margin-right', 'top' => '10%', 'rotation' => -8  ),
		array( 'type' => 'brushstroke', 'color' => '--kiyose-color-burgundy',     'opacity' => 0.25, 'size' => 'sm', 'position' => 'margin-left',  'top' => '20%', 'rotation' => 5   ),
		array( 'type' => 'spiral',      'color' => '--kiyose-color-accent',       'opacity' => 0.30, 'size' => 'md', 'position' => 'margin-right', 'top' => '30%', 'rotation' => -15 ),
		array( 'type' => 'blob',        'color' => '--kiyose-color-warm-yellow',  'opacity' => 0.35, 'size' => 'sm', 'position' => 'margin-left',  'top' => '42%', 'rotation' => 10  ),
		array( 'type' => 'splash',      'color' => '--kiyose-color-accent',       'opacity' => 0.28, 'size' => 'lg', 'position' => 'margin-right', 'top' => '55%', 'rotation' => 6   ),
		array( 'type' => 'brushstroke', 'color' => '--kiyose-color-burgundy',     'opacity' => 0.30, 'size' => 'md', 'position' => 'margin-left',  'top' => '68%', 'rotation' => -10 ),
		array( 'type' => 'blob',        'color' => '--kiyose-color-warm-yellow',  'opacity' => 0.32, 'size' => 'sm', 'position' => 'margin-right', 'top' => '80%', 'rotation' => 18  ),
		array( 'type' => 'spiral',      'color' => '--kiyose-color-accent',       'opacity' => 0.25, 'size' => 'md', 'position' => 'margin-left',  'top' => '91%', 'rotation' => -5  ),

		// --- Content shapes (sous le contenu, desktop uniquement) ---
		array( 'type' => 'blob',        'color' => '--kiyose-color-warm-yellow',  'opacity' => 0.10, 'size' => 'lg', 'position' => 'content-left',  'top' => '7%',  'rotation' => 8   ),
		array( 'type' => 'splash',      'color' => '--kiyose-color-accent',       'opacity' => 0.08, 'size' => 'md', 'position' => 'content-right', 'top' => '18%', 'rotation' => -12 ),
		array( 'type' => 'brushstroke', 'color' => '--kiyose-color-burgundy',     'opacity' => 0.12, 'size' => 'sm', 'position' => 'content-left',  'top' => '35%', 'rotation' => 15  ),
		array( 'type' => 'blob',        'color' => '--kiyose-color-warm-yellow',  'opacity' => 0.09, 'size' => 'lg', 'position' => 'content-right', 'top' => '50%', 'rotation' => -7  ),
		array( 'type' => 'spiral',      'color' => '--kiyose-color-accent',       'opacity' => 0.11, 'size' => 'md', 'position' => 'content-left',  'top' => '65%', 'rotation' => 20  ),
		array( 'type' => 'splash',      'color' => '--kiyose-color-burgundy',     'opacity' => 0.08, 'size' => 'sm', 'position' => 'content-right', 'top' => '78%', 'rotation' => -4  ),
		array( 'type' => 'blob',        'color' => '--kiyose-color-warm-yellow',  'opacity' => 0.10, 'size' => 'md', 'position' => 'content-left',  'top' => '88%', 'rotation' => 11  ),
	);
}
```

> **Note** : les types et couleurs de chaque shape sont fixes pour garantir la cohérence visuelle entre visites. L'aléatoire existant dans `decorative-shapes.php` s'applique au choix de la **variante** SVG (ex : parmi les 3 variantes de splash) — ce comportement est conservé.

### 2. Template part de rendu — `template-parts/page-shapes-container.php`

```php
<?php
/**
 * Renders the decorative shapes container for non-homepage templates.
 * Called from footer.php. No output if no shapes are configured.
 *
 * @package Kiyose
 * @since   2.x.0
 */

$kiyose_shapes = kiyose_get_page_shapes();

if ( empty( $kiyose_shapes ) ) {
	return;
}
?>
<div class="deco-page-shapes" aria-hidden="true" data-deco-density-spacing="180">
	<?php foreach ( $kiyose_shapes as $kiyose_shape ) : ?>
		<?php
		get_template_part(
			'template-parts/decorative',
			'shapes',
			$kiyose_shape
		);
		?>
	<?php endforeach; ?>
</div>
```

L'attribut `data-deco-density-spacing="180"` communique au JS l'espacement minimum en pixels entre shapes (voir section densité dynamique).

### 3. Injection dans `footer.php`

Ajouter avant la fermeture `</main>` dans `footer.php` :

```php
<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
```

**Note** : sur la homepage, `kiyose_get_page_shapes()` retourne `null` → le template part ne produit aucun output. Aucune duplication avec les shapes existantes de `page-home.php`.

### 4. Enregistrement dans `functions.php`

```php
require_once get_template_directory() . '/inc/decorative-config.php';
```

### 5. Footer au-dessus des shapes — `footer.css`

Les shapes positionnées à des pourcentages élevés (ex : 91%) peuvent déborder visuellement sous le footer. Ajouter sur `.site-footer` :

```css
.site-footer {
    position: relative;
    z-index: 1;
}
```

Cela garantit que le footer empile au-dessus des shapes absolument positionnées dans `.site-main`, sans impacter le layout.

### 6. Densité dynamique — enrichissement de `deco-overlap.js`

#### Comportement

PHP rend un pool complet de shapes couvrant l'intégralité de la hauteur de page (2%-95%). JS intervient après le rendu pour masquer les shapes en excès selon la hauteur réelle du container :

1. Lire `data-deco-density-spacing` sur `.deco-page-shapes` (espacement minimum, ex: `180`)
2. Mesurer la hauteur de `.site-main`
3. Pour chaque shape, calculer sa position pixel absolue : `top_px = top_percent * container_height / 100`
4. Parcourir les shapes par type (margin-left, margin-right, content-left, content-right) séparément
5. Pour chaque série : si l'écart avec la shape précédente visible est < `spacing_min`, masquer la shape (`deco-density-hidden`)
6. Exécuter au chargement et sur `resize` (debouncé 300ms)

```js
/**
 * Adjusts visible shapes based on container height and minimum pixel spacing.
 *
 * @param {HTMLElement} container - The .deco-page-shapes element.
 */
function kiyose_apply_density_control( container ) {
	const spacingMin = parseInt( container.dataset.decoDensitySpacing, 10 ) || 180;
	const siteMain   = container.closest( '.site-main' );

	if ( ! siteMain ) {
		return;
	}

	const containerHeight = siteMain.getBoundingClientRect().height;

	// Group shapes by position column.
	const groups = {
		'margin-left':  [],
		'margin-right': [],
		'content-left': [],
		'content-right': [],
	};

	container.querySelectorAll( '.deco-shape' ).forEach( ( shape ) => {
		for ( const key of Object.keys( groups ) ) {
			if ( shape.classList.contains( `deco-shape--${key}` ) ) {
				groups[ key ].push( shape );
				break;
			}
		}
	} );

	// For each column: reset, then hide shapes that are too close.
	Object.values( groups ).forEach( ( shapes ) => {
		let lastVisiblePx = -Infinity;

		shapes.forEach( ( shape ) => {
			shape.classList.remove( 'deco-density-hidden' );
			const topPercent = parseFloat( shape.style.getPropertyValue( '--kiyose-deco-top' ) ) || 0;
			const topPx      = ( topPercent / 100 ) * containerHeight;

			if ( topPx - lastVisiblePx < spacingMin ) {
				shape.classList.add( 'deco-density-hidden' );
			} else {
				lastVisiblePx = topPx;
			}
		} );
	} );
}
```

#### CSS associé

Dans `kintsugi.css` :
```css
.deco-density-hidden {
	display: none;
}
```

### 6. Suppression du gate homepage sur le JS

**`inc/enqueue.php`** — dans `kiyose_enqueue_decorative_reveal()`, supprimer les lignes :

```php
if ( ! is_page_template( 'templates/page-home.php' ) ) {
    return;
}
```

Le JS de reveal et de collision (`decorative-reveal.js`, `deco-overlap.js`) est désormais chargé sur toutes les pages. Il gère déjà le cas où aucun élément décoratif n'est présent (querySelectorAll retourne une NodeList vide — aucune erreur, aucun coût).

Mettre à jour le docblock de la fonction :
```php
/**
 * Loads scroll-triggered reveal and overlap resolution scripts for decorative shapes.
 * Loaded on all pages — scripts gracefully handle pages with no decorative elements.
 *
 * @since 2.x.0
 * @return void
 */
```

## Accessibilité

| Critère | Spécification |
|---------|--------------|
| Aria | `aria-hidden="true"` sur `.deco-page-shapes` — invisible aux lecteurs d'écran |
| Interactivité | `pointer-events: none` sur toutes les shapes — aucun piège clavier |
| Contraste | Shapes sous le contenu (z-index 0), opacité basse content shapes (0.08-0.12) — aucun impact sur les ratios de contraste texte |
| `prefers-reduced-motion` | Parallax et float déjà désactivés par `decorative-reveal.js` — aucune modification |
| Focus | Les shapes n'interceptent pas le focus (pointer-events: none) |

## Hors périmètre

- Scribbles (`.deco-element`) sur les pages hors homepage — positionnement spécifique à chaque section
- Wave separators — transitions de couleur spécifiques à la structure de la homepage
- Modification des shapes existantes de `page-home.php`
- Interface admin (Customizer, ACF) pour configurer les shapes par page
- Choix de la variante SVG par page (l'aléatoire de `decorative-shapes.php` est conservé)

## Fichiers à créer / modifier

### Nouveaux fichiers

```
latelierkiyose/
├── inc/decorative-config.php               # Config centralisée (kiyose_get_page_shapes)
└── template-parts/page-shapes-container.php  # Template part de rendu
```

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `functions.php` | `require_once` pour `inc/decorative-config.php` |
| `footer.php` | `get_template_part('template-parts/page-shapes', 'container')` avant `</main>` |
| `inc/enqueue.php` | Supprimer le gate `is_page_template` de `kiyose_enqueue_decorative_reveal()` |
| `assets/js/modules/deco-overlap.js` | Ajouter `kiyose_apply_density_control()` + appel au chargement et resize |
| `assets/css/components/kintsugi.css` | Ajouter `.deco-density-hidden { display: none; }` |

### Fichiers non modifiés

| Fichier | Raison |
|---------|--------|
| `templates/page-home.php` | Shapes existantes hardcodées préservées |
| `template-parts/decorative-shapes.php` | Réutilisé tel quel |
| `assets/js/modules/decorative-reveal.js` | Fonctionne sans modification |

## Tests et validation

### Fonctionnel

- [ ] Shapes apparaissent sur toutes les pages hors homepage : services, about, contact, calendar, pages génériques, single posts, archives, search, 404
- [ ] La homepage n'affiche pas de shapes dupliquées
- [ ] Shapes margin visibles sur mobile (margin-left/right)
- [ ] Shapes content masquées sous 768px (CSS existant)
- [ ] Le pool PHP génère bien 16 shapes (à vérifier avec l'inspecteur)

### Densité dynamique

- [ ] Page longue (> 2000px) : au moins 7-8 margin shapes visibles
- [ ] Page courte (< 700px, ex: 404) : 2-3 margin shapes visibles maximum
- [ ] Resize fenêtre : densité recalculée après 300ms

### Accessibilité

- [ ] Contraste texte/fond non impacté sur toutes les pages — vérifier aux emplacements où les shapes margin sont visibles
- [ ] Navigation clavier non impactée (Tab ne passe pas sur les shapes)
- [ ] Shapes absentes de l'arbre d'accessibilité (aria-hidden)
- [ ] `prefers-reduced-motion: reduce` → parallax désactivé sur toutes les pages

### Responsive

- [ ] Rendu correct à 320px (shapes margin uniquement, shapes content masquées)
- [ ] Rendu correct à 768px (transition mobile/desktop)
- [ ] Rendu correct à 1280px et 1920px

### Standards

- [ ] `make test` passe (PHPCS, ESLint, Stylelint)
- [ ] Aucune erreur PHPCS dans `inc/decorative-config.php` et `template-parts/page-shapes-container.php`
- [ ] Aucune erreur ESLint dans `deco-overlap.js` après modification
