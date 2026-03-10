# PRD 0028 — Cadre de contenu important (callout box)

## Statut

**terminé**

## Objectif

Créer un composant réutilisable de type « callout box » pour mettre en valeur un contenu important (phrase-clé, avertissement, information notable). Le composant est conçu pour les pages de services mais utilisable partout dans le site via un shortcode WordPress.

## Contexte

Certaines pages de services ont besoin de mettre en évidence un contenu textuel important (ex. : mention légale, information tarifaire, message-clé). Aucun composant de ce type n'existe actuellement dans le thème. Le blockquote Gutenberg (`<blockquote>`) a un style spécifique « citation avec bordure gauche dorée » (défini dans `about-page.css`) qui ne correspond pas visuellement à un cadre d'emphase plein fond.

## Dépendances

- [x] [0002 — Design tokens et polices](../0002-design-tokens/) (variables CSS)

## Spécifications

### Visuel

- Arrière-plan : Crème doré (`--kiyose-color-cream-gold`, `#FDF0D5`)
- Bordure : Bordure gauche épaisse (4px) en or miel (`--kiyose-color-gold`, `#E6A528`) pour renforcer la mise en avant
- Coins : Léger arrondi (`--kiyose-radius-sm` ou `4px`)
- Padding : Généreux (`--kiyose-spacing-lg` — 24px)
- Largeur : 100% de la zone de contenu parente (pas de dépassement)

### Contrastes vérifiés

| Texte                                              | Fond | Ratio   | WCAG AA |
| -------------------------------------------------- | ---- | ------- | ------- |
| Bordeaux `#5D0505` sur Crème doré `#FDF0D5`        | —    | 12.49:1 | ✅      |
| Texte principal `#333333` sur Crème doré `#FDF0D5` | —    | 11.19:1 | ✅      |

Le fond crème doré offre un contraste excellent avec les couleurs de texte du thème. C'est le choix préféré car il est plus subtil que les jaunes saturés de la palette tout en restant visuellement distinct du fond principal beige clair (`#EFE5E4`).

### Contenu

- Le contenu peut être une phrase unique ou plusieurs paragraphes
- Le HTML autorisé à l'intérieur du cadre est le contenu WordPress standard filtré par `wp_kses_post()` : paragraphes, listes, liens, gras, italique, etc.
- Pas de titre obligatoire, mais un attribut optionnel `titre` peut ajouter un heading (`<p>` bold, pas un `<hN>` pour éviter les problèmes de hiérarchie de headings)

### Shortcode

```
[kiyose_callout]Contenu important ici[/kiyose_callout]
[kiyose_callout titre="Bon à savoir"]Contenu avec titre optionnel[/kiyose_callout]
```

**Attributs :**

| Attribut | Type   | Défaut   | Description                                          |
| -------- | ------ | -------- | ---------------------------------------------------- |
| `titre`  | string | _(vide)_ | Titre optionnel affiché en gras au-dessus du contenu |

## Fichiers à créer / modifier

### Nouveaux fichiers

```
latelierkiyose/
├── assets/css/components/callout-box.css       # Styles du composant
└── assets/css/components/callout-box.min.css   # Version minifiée
```

### Fichiers modifiés

| Fichier              | Modification                                                       |
| -------------------- | ------------------------------------------------------------------ |
| `inc/shortcodes.php` | Ajout de `kiyose_callout_shortcode()` + `add_shortcode()`          |
| `inc/enqueue.php`    | Enqueue conditionnel de `kiyose-callout-box` via `has_shortcode()` |

## Spécifications techniques

### Structure HTML

```html
<div class="callout-box" role="note">
	<!-- Si titre présent -->
	<p class="callout-box__title"><strong>Bon à savoir</strong></p>
	<!-- Contenu (filtré par wp_kses_post + wpautop) -->
	<div class="callout-box__content">
		<p>Contenu important ici.</p>
	</div>
</div>
```

- `role="note"` : indique aux technologies d'assistance qu'il s'agit d'un contenu complémentaire notable (cf. [WAI-ARIA `note` role](https://www.w3.org/TR/wai-aria-1.2/#note))
- Pas de `role="alert"` : le contenu n'est pas dynamique ni urgent

### CSS (BEM)

```css
.callout-box {
	background-color: var(--kiyose-color-cream-gold);
	border-left: 4px solid var(--kiyose-color-gold);
	border-radius: 4px;
	padding: var(--kiyose-spacing-lg);
	margin-block: var(--kiyose-spacing-lg);
	color: var(--kiyose-color-burgundy);
}

.callout-box__title {
	font-weight: 700;
	margin-block: 0 var(--kiyose-spacing-sm);
}

.callout-box__content > :last-child {
	margin-bottom: 0;
}
```

### PHP — Shortcode

```php
function kiyose_callout_shortcode( $atts, $content = null ) {
	if ( empty( $content ) ) {
		return '';
	}

	$args = shortcode_atts(
		array(
			'titre' => '',
		),
		$atts,
		'kiyose_callout'
	);

	$titre = sanitize_text_field( $args['titre'] );

	$output  = '<div class="callout-box" role="note">';

	if ( ! empty( $titre ) ) {
		$output .= '<p class="callout-box__title"><strong>' . esc_html( $titre ) . '</strong></p>';
	}

	$output .= '<div class="callout-box__content">';
	$output .= wp_kses_post( wpautop( do_shortcode( $content ) ) );
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'kiyose_callout', 'kiyose_callout_shortcode' );
```

### Enqueue conditionnel

Dans `inc/enqueue.php`, ajouter la vérification `has_shortcode()` dans le hook existant :

```php
if ( is_singular() && has_shortcode( $post->post_content, 'kiyose_callout' ) ) {
	wp_enqueue_style(
		'kiyose-callout-box',
		get_template_directory_uri() . '/assets/css/components/callout-box' . kiyose_get_asset_suffix() . '.css',
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( 'assets/css/components/callout-box.css' )
	);
}
```

## Accessibilité

| Critère                  | Spécification                                                                                     |
| ------------------------ | ------------------------------------------------------------------------------------------------- |
| Contraste texte/fond     | ≥ 4.5:1 — validé (12.49:1 min.)                                                                   |
| Rôle ARIA                | `role="note"` — signale le contenu notable                                                        |
| Navigation clavier       | Aucun élément interactif propre — n/a (les liens dans le contenu restent tabulables normalement)  |
| Lecteur d'écran          | Le `role="note"` permet aux utilisateurs de comprendre qu'il s'agit d'un complément d'information |
| `prefers-reduced-motion` | Pas d'animation — n/a                                                                             |

## Hors périmètre

- Variantes de couleur (succès/erreur/info) : pas de besoin identifié, à ajouter plus tard si nécessaire
- Icône dans le cadre : pas dans le scope initial
- Bloc Gutenberg natif : le shortcode suffit pour l'usage prévu ; un bloc dédié pourra être envisagé si le composant est utilisé fréquemment

## Tests et validation

### Fonctionnel

- [x] Le shortcode `[kiyose_callout]...[/kiyose_callout]` affiche le cadre
- [x] Le shortcode avec `titre` affiche le titre en gras
- [x] Le shortcode sans contenu ne rend rien (pas de cadre vide)
- [x] Le contenu multi-paragraphes s'affiche correctement
- [x] Les shortcodes imbriqués dans le contenu sont traités (`do_shortcode`)
- [x] Le CSS n'est chargé que sur les pages contenant le shortcode

### Accessibilité

- [x] Contraste validé avec WebAIM Contrast Checker
- [x] `role="note"` présent dans le HTML généré
- [ ] Lecteur d'écran (VoiceOver) annonce le composant comme une note
- [x] Pas de piège clavier si des liens sont présents dans le contenu

### Responsive

- [x] Rendu correct à 320px (padding réduit si nécessaire)
- [ ] Rendu correct sur tablette et desktop
- [x] Pas de débordement horizontal

### Standards

- [x] `make phpcs` passe sans erreur sur les fichiers modifiés
- [x] `make test` passe
