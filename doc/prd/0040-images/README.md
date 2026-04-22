# PRD 0040 — Positionnement des images dans le contenu éditorial

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-04-22

## Contexte

Le thème permet aux rédacteurices d'insérer des images dans le contenu des pages et articles via l'éditeur Gutenberg. Aujourd'hui, **aucun style n'est défini** pour les alignements `.alignleft`, `.alignright`, `.aligncenter` : les images s'affichent avec la taille native du fichier uploadé, sans contrôle de largeur, sans flottement de texte, et sans gestion responsive.

Résultat actuel : rendu imprévisible, images souvent trop grandes en desktop, pas de texte flottant autour, captions non stylées.

Seul style existant : `.blog-single__content img { margin: 2xl 0; max-width: 100% }` dans `blog-single.css` (lignes 114-119). Il sera remplacé.

## Objectif

Définir des règles CSS globales pour les images insérées dans le contenu éditorial (bloc Gutenberg `wp-block-image` et classes core WordPress), utilisables sur **toutes les pages** qui rendent du contenu rédactionnel.

Trois comportements :

1. **Centré** : tous viewports → largeur max **720px**, centré, pas de texte flottant
2. **Gauche/Droite** : uniquement desktop (≥ 1024px) → largeur **320px**, texte flottant autour
3. **Viewports < 1024px** : l'alignement gauche/droite se replie automatiquement en centré pleine largeur (100%)

Le rédacteurice utilise la **toolbar native de Gutenberg** (bouton Aligner gauche/centre/droite du bloc Image) — zéro UX custom à apprendre.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Mécanique d'alignement | Classes natives Gutenberg (`.alignleft`, `.alignright`, `.aligncenter`) | Zéro bloc custom, UX rédacteur inchangée, markup standard WP |
| Largeur desktop flottant | `320px` (≈ 1/3 de la colonne de contenu) | Laisse 2/3 au texte ; suffisant pour lire sans veuves/orphelines |
| Largeur image centrée | `720px` fixe (max) | Plus étroit que le contenu éditorial (120ch) pour images non intrusives |
| Breakpoint flottant | `≥ 1024px` (desktop) | Texte flottant illisible en-dessous. Cohérent avec les autres breakpoints du thème (767/768/1024) |
| Responsive < 768px | `width: 100%` | Image pleine colonne |
| Responsive 768-1023px | `width: 100%` centré (même `.alignleft`/`.alignright`) | Pas de flottant sur tablet (texte trop court autour) |
| Scope CSS | Règles globales sur `.wp-block-image` + classes `align*` | Appliqué partout où `the_content()` est utilisé (blog, pages, templates custom) |
| Captions | Stylées (`figcaption` / `.wp-caption-text` / `.wp-element-caption`) | Italique, `font-size-sm`, centrées |
| `.alignwide` / `.alignfull` | **Hors périmètre** | Non demandé ; simplifier la surface |
| Nouvelle `add_image_size()` | **Non nécessaire** | Le `srcset` natif WordPress (tailles `medium`, `medium_large`, `large`) suffit — le navigateur pioche la bonne source via `max-width` CSS |

## Dépendances

- **PRD 0002** : Design tokens (variables `--kiyose-spacing-*`, `--kiyose-font-size-*`)
- **PRD 0022** : Images responsives (hero + thumbnails) — périmètre distinct, ne touche pas au contenu éditorial

## Spécifications CSS

### 1. Ajouter une section dans `gutenberg-blocks.css`

À ajouter à la suite des sections existantes (après `BUTTONS GROUP`) :

```css
/* ============================================
   IMAGES (wp-block-image)

   Positionnement des images dans le contenu éditorial.
   S'appuie sur les classes natives Gutenberg.

   Comportement :
   - Centré : max 720px, pas de flottant
   - Gauche/Droite : flottant actif uniquement ≥1024px (320px)
   - < 1024px : tous alignements → centré pleine largeur
   ============================================ */

/* Base — image responsive par défaut */
.wp-block-image {
	margin: var(--kiyose-spacing-2xl) auto;
	max-width: 100%;
}

.wp-block-image img {
	border-radius: 4px;
	display: block;
	height: auto;
	max-width: 100%;
}

/* Centré — tous viewports */
.wp-block-image.aligncenter,
figure.aligncenter {
	margin-inline: auto;
	max-width: min(100%, 720px);
}

.wp-block-image.aligncenter img,
figure.aligncenter img {
	margin-inline: auto;
}

/* Mobile & tablet (< 1024px) :
   `.alignleft` et `.alignright` se comportent comme `.aligncenter`
   (pas de flottant, centré, pleine largeur) */
.wp-block-image.alignleft,
.wp-block-image.alignright,
figure.alignleft,
figure.alignright {
	margin-inline: auto;
	max-width: 100%;
}

/* Desktop (≥ 1024px) — flottant activé */
@media (width >= 1024px) {
	.wp-block-image.alignleft,
	figure.alignleft {
		float: left;
		margin: var(--kiyose-spacing-sm) var(--kiyose-spacing-xl) var(--kiyose-spacing-lg) 0;
		max-width: 320px;
	}

	.wp-block-image.alignright,
	figure.alignright {
		float: right;
		margin: var(--kiyose-spacing-sm) 0 var(--kiyose-spacing-lg) var(--kiyose-spacing-xl);
		max-width: 320px;
	}
}

/* Captions — figcaption (Gutenberg) + .wp-caption-text (Classic Editor) */
.wp-block-image figcaption,
.wp-element-caption,
.wp-caption-text {
	color: var(--kiyose-color-text-light);
	font-size: var(--kiyose-font-size-sm);
	font-style: italic;
	margin-top: var(--kiyose-spacing-xs);
	text-align: center;
}

/* Conteneurs de contenu — `flow-root` pour contenir les floats
   et éviter qu'une image flottante déborde sur la section suivante */
.blog-single__content,
.page__content {
	display: flow-root;
}
```

### 2. Nettoyage dans `blog-single.css`

Supprimer les lignes 114-119 (bloc `.blog-single__content img`) — devient redondant et potentiellement en conflit avec `figure.wp-block-image img` :

```css
/* À SUPPRIMER */
.blog-single__content img {
	border-radius: 4px;
	height: auto;
	margin: var(--kiyose-spacing-2xl) 0;
	max-width: 100%;
}
```

### 3. Minification

Régénérer `gutenberg-blocks.min.css` et `blog-single.min.css`.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/css/components/gutenberg-blocks.css` | Ajouter section `IMAGES (wp-block-image)` |
| `latelierkiyose/assets/css/components/gutenberg-blocks.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-single.css` | Supprimer bloc `.blog-single__content img` (l. 114-119) |
| `latelierkiyose/assets/css/components/blog-single.min.css` | Régénérer |

**Aucun PHP à modifier** : markup natif WP/Gutenberg, aucune nouvelle `add_image_size()`.

## Accessibilité

| Critère | Validation |
|---|---|
| Contraste caption | `--kiyose-color-text-light` (#666666) — ratio 4.76:1 sur fond beige, WCAG AA ✅ |
| Attribut `alt` | Géré par l'éditeur WordPress (champ dans le bloc Image) — hors périmètre CSS |
| Navigation clavier | Non impactée (images non interactives) |
| Touch targets | Non applicable |
| Responsive 320px minimum | `width: 100%` = largeur colonne – paddings parent, toujours lisible |

## Hors périmètre

- `.alignwide` et `.alignfull` — non demandés (simplifier la surface)
- Bloc Gutenberg custom `kiyose/image-aligned` — on garde les blocs natifs
- Nouvelle taille `add_image_size()` — `srcset` natif WordPress suffit
- Lightbox / zoom au clic — PRD dédié si besoin
- Images dans les templates non-éditoriaux (hero, cards, meta-boxes) — traité par PRD 0022
- Galeries (`wp-block-gallery`) — à traiter séparément si besoin

## Tests et validation

### Article de test à créer
Article de blog avec 3 images :
- Image alignée à gauche + paragraphe long
- Image centrée avec caption
- Image alignée à droite + paragraphe long

### Visuel desktop (≥ 1024px)
- [ ] `.alignleft` : flotte à gauche, largeur 320px, texte à droite
- [ ] `.alignright` : flotte à droite, largeur 320px, texte à gauche
- [ ] `.aligncenter` : centrée, max 720px, texte au-dessus/dessous uniquement
- [ ] Aucun débordement du float sur la section suivante (Kintsugi shapes, footer)
- [ ] Caption italique, plus petite, centrée sous l'image

### Visuel tablet (768-1023px)
- [ ] Toutes les images (gauche/droite/centre) sont centrées, pleine colonne
- [ ] Aucun flottement

### Visuel mobile (320-767px)
- [ ] Toutes les images à 100% de la colonne
- [ ] Lisibilité conservée à 320px

### Scope pages
- [ ] Rendu correct sur `single.php` (article blog) → `.blog-single__content`
- [ ] Rendu correct sur `page.php` (page générique) → `.page__content`
- [ ] Aucune régression sur les templates custom (services, about, contact, home, calendrier) — ces templates n'utilisent pas `the_content()` comme flot principal mais peuvent contenir du contenu éditorial via meta-boxes

### Standards
- [ ] `make test` passe (PHPCS + Stylelint + ESLint)
- [ ] Custom properties CSS toutes préfixées `--kiyose-`
- [ ] `gutenberg-blocks.min.css` et `blog-single.min.css` régénérés
- [ ] Aucune régression visuelle sur les boutons et autres blocs Gutenberg existants
