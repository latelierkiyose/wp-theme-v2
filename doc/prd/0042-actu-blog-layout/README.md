# PRD 0042 — Actualités / Blog : layout de la page liste

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

La page Actualités / Blog est visible en local à l'URL :

```text
http://localhost:8000/actus-blog/
```

Elle n'est actuellement pas mise en forme comme une vraie page de liste d'articles. Le thème possède déjà :

- `archive.php`, qui rend les archives WordPress avec `.blog-archive` et une grille d'articles.
- `template-parts/content-blog.php`, réutilisé pour la home et les listes.
- `searchform.php`, qui fournit un formulaire de recherche accessible.
- `blog-card.css`, qui définit une carte verticale, utilisée notamment sur la home.

Point critique : `/actus-blog/` correspond très probablement à la **page des articles WordPress** configurée dans Réglages → Lecture. Sans template `home.php`, WordPress peut utiliser `index.php` comme fallback pour cette page. Une correction limitée à `archive.php` risque donc de ne pas corriger l'URL réellement observée.

## Objectif

Créer une mise en page éditoriale claire pour la liste Actualités / Blog :

1. Afficher un titre centré `Actus / Blog`.
2. Afficher les articles en **2 colonnes** sur desktop, avec retour à 1 colonne sur mobile/tablet.
3. Ajouter une colonne latérale à droite avec :
   - un bloc `Recherche`,
   - un bloc `Catégories`.
4. Pour chaque article, afficher l'image à la une sous le titre et à gauche de l'extrait, dans l'esprit de la page calendrier.
5. Préserver le rendu actuel des cartes de blog sur la page d'accueil.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Template WordPress principal | Créer `home.php` pour la page des articles | Cible la hiérarchie WordPress correcte pour `/actus-blog/` quand cette page est configurée comme page des articles |
| Archives catégories/dates/tags | Appliquer le même layout à `archive.php` | Les catégories de la sidebar mènent vers des archives ; l'expérience doit rester cohérente |
| Fallback générique | Ne pas modifier `index.php` | `index.php` doit rester un fallback minimal, pas porter un layout métier |
| Sidebar | Créer un template part dédié `template-parts/blog-sidebar.php` | Réutilisable entre `home.php` et `archive.php`, évite la duplication |
| Recherche | Réutiliser `get_search_form()` | Formulaire existant, déjà accessible et stylé via `search.css` |
| Catégories | Utiliser `wp_list_categories()` avec `hide_empty => true` | Ne montre que les catégories utiles côté visiteur |
| Carte article | Ajouter une variante liste/archive sans casser la home | La home dépend du rendu `.blog-card` vertical actuel ; la nouvelle mise en page ne doit pas la modifier globalement |
| Image à la une | Sous le titre, à gauche de l'extrait à partir de 768px | Respecte le croquis fourni et garde une lecture simple sur mobile |
| Absence d'image | Garder une carte compacte sans placeholder décoratif | Évite d'inventer une image ou de créer un bloc vide |
| Pagination | Conserver `the_posts_pagination()` | Comportement WordPress natif, déjà présent dans `archive.php` |

## Spécifications fonctionnelles

### 1. Page des articles — `home.php`

Créer `latelierkiyose/home.php`.

Structure attendue :

```php
<main id="main" class="site-main" tabindex="-1">
	<section class="blog-archive blog-archive--index" aria-labelledby="blog-archive-title">
		<header class="blog-archive__header">
			<h1 id="blog-archive-title" class="blog-archive__title">Actus / Blog</h1>
		</header>

		<div class="blog-archive__layout">
			<div class="blog-archive__main">
				<div class="blog-archive__grid">
					<!-- articles -->
				</div>
				<!-- pagination -->
			</div>

			<aside class="blog-archive__sidebar" aria-label="Navigation des actualités">
				<!-- recherche + catégories -->
			</aside>
		</div>
	</section>
</main>
```

Règles :

- Le titre visible est toujours `Actus / Blog`, quel que soit le titre de la page WordPress assignée à la page des articles.
- La boucle utilise le loop principal WordPress (`have_posts()`, `the_post()`), sans `WP_Query` custom.
- Les articles utilisent `get_template_part( 'template-parts/content', 'blog', array( 'variant' => 'archive' ) )`.
- En absence d'article, afficher `Aucun article pour le moment.` dans `.blog-archive__no-posts`.
- Inclure `template-parts/page-shapes-container.php` comme les autres templates non-home.

### 2. Archives WordPress — `archive.php`

Mettre à jour `archive.php` pour réutiliser le même shell :

- Garder `the_archive_title()` et `the_archive_description()` pour les archives catégorie/date/tag/auteur.
- Ajouter `.blog-archive__layout`, `.blog-archive__main` et `.blog-archive__sidebar`.
- Réutiliser `template-parts/blog-sidebar.php`.
- Utiliser la variante `archive` du template part article.
- Conserver la pagination existante.

### 3. Sidebar blog — `template-parts/blog-sidebar.php`

Créer un template part partagé.

Structure attendue :

```php
<div class="blog-sidebar">
	<section class="blog-sidebar__section blog-sidebar__section--search" aria-labelledby="blog-sidebar-search-title">
		<h2 id="blog-sidebar-search-title" class="blog-sidebar__title">Recherche</h2>
		<?php get_search_form(); ?>
	</section>

	<section class="blog-sidebar__section blog-sidebar__section--categories" aria-labelledby="blog-sidebar-categories-title">
		<h2 id="blog-sidebar-categories-title" class="blog-sidebar__title">Catégories</h2>
		<ul class="blog-sidebar__categories">
			<?php
			wp_list_categories(
				array(
					'title_li'   => '',
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'show_count' => false,
				)
			);
			?>
		</ul>
	</section>
</div>
```

Paramètres `wp_list_categories()` :

- `title_li` vide.
- `hide_empty` à `true`.
- `orderby` à `name`.
- `order` à `ASC`.
- `show_count` à `false`.

Si aucune catégorie contenant des articles n'existe, masquer le bloc catégories plutôt que d'afficher une liste vide. Utiliser `get_categories()` pour détecter ce cas.

### 4. Carte article archive — `template-parts/content-blog.php`

Adapter le template part pour accepter un argument :

```php
$kiyose_variant = isset( $args['variant'] ) ? sanitize_key( $args['variant'] ) : 'card';
```

Règles :

- Variante par défaut `card` : rendu actuel conservé pour la home.
- Variante `archive` : ajouter une classe modifier `blog-card--archive`.
- Ne pas déplacer le titre sous l'image pour la variante `archive`.
- Pour la variante `archive`, l'ordre visuel attendu est :
  1. date,
  2. titre,
  3. image à la une,
  4. extrait,
  5. lien `Lire la suite`.
- Les sorties existantes restent échappées ou produites par les fonctions WordPress adaptées.
- L'image garde `loading => lazy`.
- L'attribut `alt` garde le titre de l'article, comme aujourd'hui.

Markup cible simplifié pour la variante archive :

```html
<article class="blog-card blog-card--archive">
	<div class="blog-card__content">
		<header class="blog-card__header">
			<time class="blog-card__date" datetime="2026-05-02">2 mai 2026</time>
			<h2 class="blog-card__title"><a href="/article/">Titre de l'article</a></h2>
		</header>
		<div class="blog-card__body">
			<div class="blog-card__thumbnail"><a href="/article/"><img src="image.jpg" alt="Titre de l'article"></a></div>
			<div class="blog-card__excerpt"><p>Extrait de l'article.</p></div>
		</div>
		<a class="blog-card__read-more">Lire la suite</a>
	</div>
</article>
```

Si l'article n'a pas d'image à la une, `.blog-card__body` ne contient que l'extrait et ne réserve pas d'espace vide.

## Spécifications CSS

Modifier `latelierkiyose/assets/css/components/blog-archive.css`.

### Layout global

- `.blog-archive` conserve son `max-width: var(--kiyose-container-max-width)`.
- `.blog-archive__layout` :
  - mobile/tablet : `display: block`.
  - desktop `>= 1024px` : grid avec colonne principale flexible et sidebar fixe.
- Colonnes desktop recommandées :

```css
.blog-archive__layout {
	display: grid;
	gap: var(--kiyose-spacing-2xl);
	grid-template-columns: minmax(0, 1fr) minmax(260px, 320px);
}
```

### Grille articles

- `.blog-archive__grid` :
  - mobile `<= 767px` : 1 colonne.
  - tablet `768px - 1023px` : 1 colonne, pour éviter des cartes trop étroites avec sidebar empilée.
  - desktop `>= 1024px` : 2 colonnes.
- Supprimer ou remplacer la règle actuelle desktop à 3 colonnes.

### Sidebar

Styles attendus :

- `.blog-sidebar` : largeur pleine en mobile, sticky non requis.
- `.blog-sidebar__section` : fond blanc, bordure légère, padding, `border-radius: 8px`.
- `.blog-sidebar__title` : `font-family: var(--kiyose-font-body)`, `font-size: var(--kiyose-font-size-lg)`, `font-weight: 700`, couleur bordeaux.
- `.blog-sidebar__categories` : liste sans puces natives.
- Liens catégories :
  - hauteur/touch target confortable,
  - contraste AA,
  - underline ou changement visible au hover/focus,
  - `focus-visible` avec outline bordeaux.

### Variante `blog-card--archive`

Modifier `latelierkiyose/assets/css/components/blog-card.css`.

Règles attendues :

- La carte archive reste en fond blanc, mais peut être moins haute que la carte home.
- `.blog-card--archive .blog-card__body` :
  - mobile : bloc vertical.
  - `>= 768px` : grid avec image à gauche et extrait à droite.
- Largeur image recommandée en mode horizontal : `140px` à `180px`, avec ratio stable.
- L'image est sous le titre, jamais au-dessus.
- Le CTA reste accessible et visible.
- Les animations hover/focus respectent `prefers-reduced-motion`.

### Minification

Régénérer :

- `latelierkiyose/assets/css/components/blog-archive.min.css`
- `latelierkiyose/assets/css/components/blog-archive.min.css.map`
- `latelierkiyose/assets/css/components/blog-card.min.css`
- `latelierkiyose/assets/css/components/blog-card.min.css.map`

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/home.php` | Nouveau template pour la page des articles |
| `latelierkiyose/archive.php` | Réutiliser le layout avec sidebar pour les archives |
| `latelierkiyose/template-parts/blog-sidebar.php` | Nouveau template part sidebar recherche + catégories |
| `latelierkiyose/template-parts/content-blog.php` | Ajouter la variante `archive` sans casser la variante par défaut |
| `latelierkiyose/assets/css/components/blog-archive.css` | Ajouter layout principal + sidebar + grille 2 colonnes |
| `latelierkiyose/assets/css/components/blog-archive.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-archive.min.css.map` | Régénérer |
| `latelierkiyose/assets/css/components/blog-card.css` | Ajouter les styles `blog-card--archive` |
| `latelierkiyose/assets/css/components/blog-card.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-card.min.css.map` | Régénérer |
| `doc/user/02-choisir-un-template.md` | Documenter la page des articles automatique |
| `doc/release/content-migration.md` | Documenter la configuration de la page des articles |

## Accessibilité

| Critère | Validation |
|---|---|
| Structure landmarks | `main` unique, sidebar en `aside`, sections sidebar labellisées |
| Titres | Un seul `h1` visible sur `/actus-blog/`, titres sidebar en `h2` |
| Recherche | `get_search_form()` conserve un label accessible |
| Navigation clavier | Champ recherche, bouton, catégories, titres, CTA et pagination accessibles au clavier |
| Focus visible | Tous les liens et boutons ont un état `focus-visible` contrasté |
| Touch targets | Recherche, bouton, catégories et pagination visent 44px minimum quand possible |
| Contraste | Texte et liens >= 4.5:1, états UI >= 3:1 |
| Responsive | Reflow sans scroll horizontal à partir de 320px |
| Images | `alt` présent ; image liée en `aria-hidden` seulement si le lien titre reste le lien principal |

## Documentation utilisateur et release

### `doc/user/02-choisir-un-template.md`

Mettre à jour la section "Ce qui est automatique" :

- Articles individuels : `single.php`.
- Page principale des articles : template automatique `home.php`, configurée via Réglages → Lecture.
- Archives catégorie/date/tag : `archive.php`.
- Résultats de recherche : `search.php`.

### `doc/release/content-migration.md`

Ajouter une étape de configuration :

- Réglages → Lecture → Une page statique.
- Page d'accueil : `Accueil`.
- Page des articles : `Actus / Blog` ou `Actualités`.
- Vérifier que l'URL publique attendue est `/actus-blog/`.

Cette modification impacte la configuration de production mais ne nécessite ni plugin, ni migration de données, ni nouveau champ admin.

## Hors périmètre

- Refonte de `search.php`.
- Ajout de filtres dynamiques AJAX.
- Ajout d'une recherche limitée au blog uniquement.
- Affichage des tags dans la sidebar.
- Gestion d'une image placeholder globale.
- Modification du nombre d'articles par page.
- Changement du contenu éditorial des articles.
- Changement du design de la section Actualités sur la home.

## Tests et validation

### Tests automatisés

Ajouter des tests unitaires uniquement si une fonction helper PHP est créée pour la sidebar ou la normalisation de variante. Si l'implémentation se limite à des templates WordPress natifs et du CSS, aucun test PHPUnit dédié n'est requis.

Dans tous les cas :

- [ ] `make test` passe.

### Validation fonctionnelle

- [ ] `/actus-blog/` utilise le nouveau layout et non le fallback visuel de `index.php`.
- [ ] Le titre visible est `Actus / Blog`.
- [ ] Les articles s'affichent en 2 colonnes sur desktop.
- [ ] Les articles s'affichent en 1 colonne à 320px, 768px et 1023px.
- [ ] La sidebar s'affiche à droite en desktop.
- [ ] La sidebar passe sous la liste sur mobile/tablet sans scroll horizontal.
- [ ] Le formulaire de recherche soumet vers la recherche WordPress.
- [ ] Les catégories vides ne sont pas affichées.
- [ ] Cliquer une catégorie mène vers une archive avec le même layout.
- [ ] La pagination reste visible et fonctionnelle.
- [ ] Un article sans image à la une reste lisible et ne laisse pas d'espace vide.

### Validation visuelle

- [ ] L'image à la une est sous le titre et à gauche de l'extrait à partir de 768px.
- [ ] L'extrait reste lisible et ne chevauche pas l'image.
- [ ] Les cartes de la home gardent leur rendu actuel.
- [ ] Les espacements sont cohérents avec la page calendrier.
- [ ] Les éléments décoratifs de page ne chevauchent pas les textes.

### Validation accessibilité

- [ ] Navigation clavier : Tab, Entrée, Shift+Tab sur recherche, catégories, articles et pagination.
- [ ] Focus visible sur tous les éléments interactifs.
- [ ] Contraste AA validé pour sidebar, liens, titres, extraits et pagination.
- [ ] Zoom navigateur 200% : pas de perte de contenu.
- [ ] Lecteur d'écran : le `aside` est annoncé comme navigation des actualités.

### Standards

- [ ] BEM respecté pour les nouvelles classes.
- [ ] Fonctions PHP préfixées `kiyose_` si une fonction est ajoutée.
- [ ] Text domain `kiyose` sur toutes les chaînes traduisibles.
- [ ] Sorties échappées (`esc_html`, `esc_attr`, `esc_url`) quand elles ne sont pas produites par une fonction WordPress qui échappe déjà.
- [ ] CSS custom properties existantes utilisées, toutes préfixées `--kiyose-`.
- [ ] Aucun secret, token ou identifiant sensible ajouté.
