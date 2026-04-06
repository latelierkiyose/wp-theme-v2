# PRD 0034 — Correctifs blog (page actus + blog-cards)

## Statut

**terminé**

## Objectif

Corriger le rendu de la page blog `/actus-blog/` (titre invisible, mise en page brouillon) et réduire la taille des titres dans les blog-cards.

## Contexte

### Problèmes identifiés

**1. Page `/actus-blog/` — rendu brouillon sans structure**

La page utilise `page.php` avec un bloc Gutenberg `wp:latest-posts` affichant 100 articles. Résultat :

- Aucun style custom pour `.wp-block-latest-posts` : les titres `h2` héritent du style global → **3.25rem (52px) en Dancing Script**, démesuré pour une liste.
- Pas de thumbnails, pas de structure card, pas de lien "Lire la suite" — le bloc produit un simple `<ul><li><a>`.
- Le titre h1 est masqué par la bulle du logo qui déborde du header sticky (la bulle fait jusqu'à 280px de diamètre, le contenu commence à ~48px sous le header).

**2. Blog-cards — titres `.blog-card__title` trop grands**

Les titres utilisent Dancing Script (font heading) à 1.625rem (26px). En police script, cette taille paraît surdimensionnée pour une card compacte.

**3. Bug CSS — `--kiyose-line-height-snug` indéfinie**

Référencée dans `blog-card.css:101` et `blog-single.css:342` mais jamais définie dans `variables.css`. Le `line-height` tombe en héritage sans fallback.

## Spécifications

### 1. Template `templates/page-blog.php`

Créer un template de page dédié qui réutilise les composants existants (`.blog-archive`, `.blog-card`).

```php
<?php
/**
 * Template Name: Blog / Actualités
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

$blog_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => get_option( 'posts_per_page' ),
		'paged'          => $paged,
	)
);
?>

<main id="main" class="site-main" tabindex="-1">
	<section class="blog-archive">
		<header class="blog-archive__header">
			<h1 class="blog-archive__title"><?php the_title(); ?></h1>
		</header>

		<?php if ( $blog_query->have_posts() ) : ?>
			<div class="blog-archive__grid">
				<?php
				while ( $blog_query->have_posts() ) :
					$blog_query->the_post();
					get_template_part( 'template-parts/content', 'blog' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php
			// Pagination — swap temporaire de $wp_query pour the_posts_pagination().
			global $wp_query;
			$original_query = $wp_query;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Swap temporaire pour pagination.
			$wp_query = $blog_query;

			the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => __( 'Précédent', 'kiyose' ),
					'next_text'          => __( 'Suivant', 'kiyose' ),
					'screen_reader_text' => __( 'Pagination du blog', 'kiyose' ),
				)
			);

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restauration query originale.
			$wp_query = $original_query;
			?>
		<?php else : ?>
			<p class="blog-archive__no-posts"><?php esc_html_e( 'Aucun article pour le moment.', 'kiyose' ); ?></p>
		<?php endif; ?>
	</section>
	<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
</main>

<?php
get_footer();
```

**Points clés** :

- Réutilise les classes CSS `.blog-archive` existantes (layout, grid, pagination) → aucun nouveau CSS de layout nécessaire.
- Réutilise `content-blog.php` (blog-cards avec thumbnails, extraits, "Lire la suite").
- `the_posts_pagination()` via swap temporaire de `$wp_query` → markup identique à `archive.php`. Pattern courant dans l'écosystème WordPress pour les page templates avec custom query.
- Query var : `paged` (archives) ET `page` (WordPress Pages) pour la pagination.
- `wp_reset_postdata()` après la boucle custom.
- `posts_per_page` reprend la valeur WordPress (Réglages → Lecture).
- `post_status => publish` pour ne pas afficher les brouillons.

**Migration du contenu** (action manuelle dans le back-office) :

1. Éditer la page `/actus-blog/` dans l'admin WP.
2. Changer le modèle de page vers "Blog / Actualités".
3. Supprimer le bloc `wp:latest-posts` du contenu (le contenu de la page n'est plus utilisé par le template).

### 2. CSS — `.blog-card__title` en Nunito + taille réduite

Modifier `assets/css/components/blog-card.css` :

**Avant** (lignes 98-103) :

```css
.blog-card__title {
	color: var(--kiyose-color-burgundy);
	font-size: var(--kiyose-font-size-xl);
	line-height: var(--kiyose-line-height-snug);
	margin: 0;
}
```

**Après** :

```css
.blog-card__title {
	color: var(--kiyose-color-burgundy);
	font-family: var(--kiyose-font-body);
	font-size: var(--kiyose-font-size-lg);
	font-weight: 700;
	line-height: var(--kiyose-line-height-snug);
	margin: 0;
}
```

Changements :

- `font-family: var(--kiyose-font-body)` → Nunito au lieu de Dancing Script (hérité du `h2` global).
- `font-size: var(--kiyose-font-size-lg)` → 1.25rem (20px) au lieu de 1.625rem (26px).
- `font-weight: 700` → explicite pour maintenir la hiérarchie visuelle.

**Media query mobile** — supprimer la règle `.blog-card__title` (lignes 180-182), car le `font-size` est maintenant identique desktop/mobile. Conserver le media query et `.blog-card__excerpt` :

**Avant** :

```css
@media (width <= 767px) {
	.blog-card__title {
		font-size: var(--kiyose-font-size-lg);
	}

	.blog-card__excerpt {
		font-size: var(--kiyose-font-size-sm);
	}
}
```

**Après** :

```css
@media (width <= 767px) {
	.blog-card__excerpt {
		font-size: var(--kiyose-font-size-sm);
	}
}
```

### 3. CSS — Définir `--kiyose-line-height-snug`

Ajouter dans `assets/css/variables.css`, après la ligne 69 (`--kiyose-line-height-normal: 1.5;`) :

```css
--kiyose-line-height-snug: 1.35;
```

Valeur entre `tight` (1.2) et `normal` (1.5), adaptée aux titres de cards multi-lignes.

## Accessibilité

| Critère          | Spécification                                                               |
| ---------------- | --------------------------------------------------------------------------- |
| Titre h1         | Présent et visible sur la page blog                                         |
| Hiérarchie       | h1 (titre page) → h2 (titre de chaque card)                                 |
| Blog cards       | `aria-label` sur "Lire la suite" (inchangé, via `content-blog.php`)         |
| Pagination       | `<nav>` avec `<h2 class="screen-reader-text">` via `the_posts_pagination()` |
| Contraste titres | Nunito 700 `#5D0505` sur `#ffffff` → ratio 15.48:1 ✅                       |
| Taille de cible  | Liens de pagination ≥ 44×44px (CSS existant dans `blog-archive.css`)        |

## Hors périmètre

- Chevauchement global de la bulle logo avec le contenu sur les pages utilisant `page.php` / `archive.php` (problème transversal, PRD distinct)
- Redesign complet des blog-cards
- Ajout de filtres par catégorie sur la page blog
- Modification des styles de `single.php` (article unique)

## Fichiers à créer / modifier

### Nouveau fichier

| Fichier                   | Description                    |
| ------------------------- | ------------------------------ |
| `templates/page-blog.php` | Template de page dédié au blog |

### Fichiers modifiés

| Fichier                                   | Modification                                                                                                                                                              |
| ----------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `assets/css/variables.css`                | Ajout de `--kiyose-line-height-snug: 1.35`                                                                                                                                |
| `assets/css/components/blog-card.css`     | `.blog-card__title` : ajout `font-family: body`, `font-weight: 700`, réduction `font-size` de `xl` à `lg` ; suppression de `.blog-card__title` dans le media query mobile |
| `assets/css/components/blog-card.min.css` | Régénérer (minification)                                                                                                                                                  |

### Action manuelle (back-office WP)

Assigner le template "Blog / Actualités" à la page `/actus-blog/` et supprimer le bloc `wp:latest-posts` du contenu de la page.

## Tests et validation

### Visuel

- [ ] La page `/actus-blog/` affiche un titre h1 visible
- [ ] Les articles s'affichent en blog-cards (thumbnail, titre, extrait, "Lire la suite")
- [ ] La pagination fonctionne (page 1, 2, suivant, précédent)
- [ ] Les blog-cards ont des titres en Nunito bold 20px (homepage ET page blog)
- [ ] Rendu cohérent entre la page blog et les archives (`/category/...`)

### Accessibilité

- [ ] Hiérarchie des titres correcte : h1 → h2
- [ ] Navigation clavier : Tab entre les cards, liens "Lire la suite", et la pagination
- [ ] Screen reader text présent sur la pagination
- [ ] Contraste des titres ≥ 4.5:1
- [ ] Focus visible sur tous les éléments interactifs

### Responsive

- [ ] 320px : 1 colonne, titres lisibles
- [ ] 768px+ : 2 colonnes
- [ ] 1024px+ : 3 colonnes
- [ ] Pagination adaptée sur mobile (tailles de cible WCAG)

### Standards

- [ ] `make test` passe (PHPCS, ESLint, Stylelint)
- [ ] Aucune erreur PHPCS dans `templates/page-blog.php`
