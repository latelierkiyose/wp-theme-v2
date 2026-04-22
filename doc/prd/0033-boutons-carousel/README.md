# PRD 0033 — Icônes du carousel de témoignages

## Statut

**terminé**

## Objectif

Remplacer les caractères Unicode bruts des boutons du carousel par des SVG inline cohérents avec le design system (bordeaux sur rose, `fill="currentColor"`).

## Contexte

Le carousel de témoignages expose trois boutons (précédent, pause/play, suivant). Ils utilisent tous des caractères Unicode :

| Bouton | Caractère | Problème |
|--------|-----------|---------|
| Précédent | `←` U+2190 | Flèche texte, rendu acceptable mais inconsistant |
| Pause | `⏸` U+23F8 | **Emoji coloré** (bleu/blanc selon OS) — casse la charte |
| Play | `▶` U+25B6 | Peut rendre en emoji selon la plateforme |
| Suivant | `→` U+2192 | Flèche texte, même problème que précédent |

Le bouton pause/play est particulièrement problématique : U+23F8 appartient au bloc emoji Unicode et reçoit un rendu coloré natif (bleu/blanc iOS, coloré Android) qui ignore complètement la propriété CSS `color`. Cela rompt l'harmonie bordeaux-sur-rose des boutons.

**Pattern existant** : le thème utilise déjà des SVG inline `fill="currentColor"` pour les icônes fonctionnelles (icônes sociales dans `inc/accessibility.php` via `kiyose_social_icon_svg()`). Les SVG héritent de la couleur CSS du parent → aucun besoin de hardcoder une couleur.

## Spécifications

### 1. Helper PHP — `inc/accessibility.php`

Ajouter la fonction `kiyose_carousel_icon_svg()` dans `inc/accessibility.php`, après `kiyose_social_icon_svg()` :

```php
/**
 * Generate SVG icon for carousel controls.
 *
 * @param string $icon Icon name: prev, next, pause, play.
 * @return string SVG markup with aria-hidden="true".
 */
function kiyose_carousel_icon_svg( $icon ) {
	$icons = array(
		'prev'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/></svg>',
		'next'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>',
		'pause' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>',
		'play'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8 5v14l11-7z"/></svg>',
	);

	return isset( $icons[ $icon ] ) ? $icons[ $icon ] : '';
}
```

> **Note** : `focusable="false"` est nécessaire pour empêcher les SVG inline d'être focusables dans IE/Edge Legacy.

### 2. Markup HTML — `templates/page-home.php` et `inc/shortcodes.php`

Le bouton pause/play pré-rend les deux icônes côté serveur. JS bascule uniquement l'attribut `hidden` — aucune injection de markup à runtime.

**Avant** (même markup dans les deux fichiers) :

```html
<div class="carousel__controls">
    <button class="carousel__prev" aria-label="Témoignage précédent">←</button>
    <button class="carousel__pause" aria-label="Mettre en pause le défilement">⏸</button>
    <button class="carousel__next" aria-label="Témoignage suivant">→</button>
</div>
```

**Après** :

```php
<div class="carousel__controls">
    <button class="carousel__prev" aria-label="<?php esc_attr_e( 'Témoignage précédent', 'kiyose' ); ?>">
        <?php echo kiyose_carousel_icon_svg( 'prev' ); ?>
    </button>
    <button class="carousel__pause" aria-label="<?php esc_attr_e( 'Mettre en pause le défilement', 'kiyose' ); ?>">
        <span class="carousel__icon carousel__icon--pause"><?php echo kiyose_carousel_icon_svg( 'pause' ); ?></span>
        <span class="carousel__icon carousel__icon--play" hidden><?php echo kiyose_carousel_icon_svg( 'play' ); ?></span>
    </button>
    <button class="carousel__next" aria-label="<?php esc_attr_e( 'Témoignage suivant', 'kiyose' ); ?>">
        <?php echo kiyose_carousel_icon_svg( 'next' ); ?>
    </button>
</div>
```

> **Note** : `aria-label` sur chaque bouton fournit le nom accessible. Les SVG sont `aria-hidden="true"` → les lecteurs d'écran lisent uniquement l'`aria-label` du bouton, dont la mise à jour reste gérée par JS (comportement inchangé).

### 3. Toggle JS — `assets/js/modules/carousel.js`

Remplacer les affectations `textContent` par des bascules d'attribut `hidden` sur les `<span>` d'icônes.

**`startAutoplay()`** — remplacer :
```js
this.pauseButton.textContent = '⏸';
```
par :
```js
this.pauseButton.querySelector( '.carousel__icon--pause' ).hidden = false;
this.pauseButton.querySelector( '.carousel__icon--play' ).hidden = true;
```

**`stopAutoplay()`** — remplacer :
```js
this.pauseButton.textContent = '▶';
```
par :
```js
this.pauseButton.querySelector( '.carousel__icon--pause' ).hidden = true;
this.pauseButton.querySelector( '.carousel__icon--play' ).hidden = false;
```

> **Pourquoi `hidden` plutôt que `innerHTML`** : les deux SVG sont déjà dans le DOM (rendus PHP). JS ne fait que masquer/afficher des éléments existants — aucune injection de markup à runtime, aucun risque XSS.

### 4. CSS — `assets/css/components/carousel.css`

Aucune modification de taille n'est nécessaire : les boutons sont déjà `44px × 44px` avec `display: flex; align-items: center; justify-content: center`. Le SVG 24×24 s'y centre naturellement.

Supprimer la propriété `font-size` devenue inutile sur les boutons :

**Avant** :
```css
.carousel__prev,
.carousel__next,
.carousel__pause {
    /* ... */
    font-size: var(--kiyose-font-size-lg);
    /* ... */
}
```

**Après** — supprimer la ligne `font-size`. Les autres propriétés restent inchangées.

> `color: var(--kiyose-color-primary-dark)` (bordeaux atténué `#603030`) est hérité par `fill="currentColor"` sur les SVG — aucune modification de couleur requise.

## Accessibilité

| Critère | Spécification |
|---------|--------------|
| Nom accessible | Chaque bouton a un `aria-label` explicite ; mise à jour JS conservée |
| Icône décorative | SVG avec `aria-hidden="true"` — invisible aux lecteurs d'écran |
| Focusable | `focusable="false"` sur les SVG — empêche un double focus IE/Edge |
| Contraste icône/fond | `color-primary-dark` (`#603030`) sur `color-primary` (`#D7A4A4`) — ratio ≥ 3:1 ✓ |
| Taille de cible | 44×44px inchangé — WCAG 2.2 AA ✓ |
| Clavier | Comportement inchangé (Tab, Entrée, toggle pause/play) |
| `prefers-reduced-motion` | Aucun impact (transitions CSS inchangées) |

## Hors périmètre

- Redesign visuel des boutons (formes, tailles, couleurs)
- Remplacement du carousel par un autre composant
- Ajout d'indicateurs de slide (pagination dots)
- Refactoring du JS du carousel au-delà du toggle

## Fichiers à créer / modifier

### Aucun nouveau fichier

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `inc/accessibility.php` | Ajout de `kiyose_carousel_icon_svg()` après `kiyose_social_icon_svg()` |
| `templates/page-home.php` | Markup boutons → PHP avec `kiyose_carousel_icon_svg()` + double `<span>` sur pause |
| `inc/shortcodes.php` | Même modification que `page-home.php` |
| `assets/js/modules/carousel.js` | `textContent` → `hidden` dans `startAutoplay()`/`stopAutoplay()` |
| `assets/js/modules/carousel.min.js` | Régénérer (minification) |
| `assets/css/components/carousel.css` | Supprimer `font-size` sur `.carousel__prev,.carousel__next,.carousel__pause` |
| `assets/css/components/carousel.min.css` | Régénérer (minification) |

## Tests et validation

### Visuel

- [ ] Les 3 boutons affichent des icônes bordeaux sur rose (aucun emoji coloré)
- [ ] Le bouton pause bascule bien vers l'icône play et inversement au clic
- [ ] Les icônes sont bien centrées dans les boutons circulaires
- [ ] Rendu cohérent sur Chrome, Firefox, Safari (macOS et iOS)
- [ ] Rendu cohérent sur Android Chrome

### Accessibilité

- [ ] Lecteur d'écran (VoiceOver/NVDA) : boutons annoncés par leur `aria-label` uniquement, pas de lecture du SVG
- [ ] `aria-label` du bouton pause change bien de "Mettre en pause…" ↔ "Reprendre…" au toggle
- [ ] Navigation Tab : boutons atteignables dans l'ordre prev → pause → next
- [ ] Activation Entrée/Espace : prev, next et pause/play fonctionnels au clavier
- [ ] Contraste bouton validé (≥ 3:1 pour composant UI)

### Fonctionnel

- [ ] Autoplay démarre au chargement et s'arrête au hover (comportement JS inchangé)
- [ ] Toggle pause → play et play → pause fonctionne correctement
- [ ] Le carousel fonctionne dans le shortcode `[kiyose_testimonials display="carousel"]`

### Standards

- [ ] `make test` passe (PHPCS, ESLint, Stylelint)
- [ ] Aucune erreur PHPCS dans `inc/accessibility.php` (nouveau helper)
- [ ] Aucune erreur ESLint dans `carousel.js` (suppression `textContent`, ajout `.hidden`)
