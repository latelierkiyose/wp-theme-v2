# PRD 0035 — Hauteur fixe et « Voir plus » du carousel de témoignages

## Statut

**prêt à l'implémentation**

## Objectif

Stabiliser la hauteur du carousel de témoignages pour éviter l'espace vide sous les citations courtes, et tronquer les citations longues avec un mécanisme « Voir plus » accessible.

## Contexte

Le carousel de témoignages (`page-home.php` et shortcode `[kiyose_testimonials display="carousel"]`) affiche des citations de longueurs très variables. Le conteneur `.carousel` prend la hauteur du slide le plus haut (tous les slides sont dans le flex row `carousel__track`). Les slides courts laissent un vide visible sous la citation.

**État actuel :**

- `.testimony-card` : pas de contrainte de hauteur, dimensionnement intrinsèque
- `.testimony-card__quote` : aucun `max-height`, `overflow`, ni `line-clamp`
- `.carousel` : `overflow: hidden` (horizontal uniquement en pratique), hauteur dictée par le slide le plus grand
- La citation utilise `font-size: 1.25rem` (20px) avec `line-height: 1.7` → une ligne ≈ 34px

## Spécifications

### 1. Troncature CSS — `testimony.css`

Appliquer un `line-clamp` sur `.testimony-card__quote` **uniquement dans le contexte carousel** (ne pas impacter la grille ni d'autres usages).

Utiliser le sélecteur `.carousel__slide .testimony-card__quote` pour scoper la troncature au carousel.

```css
.carousel__slide .testimony-card__quote {
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 3;
	overflow: hidden;
}
```

**Valeurs responsives :**

| Breakpoint        | `line-clamp` | Hauteur visible approximative |
| ----------------- | ------------ | ----------------------------- |
| Mobile (< 768px)  | 3            | ~82px (16px × 1.7 × 3)        |
| Desktop (≥ 768px) | 3            | ~102px (20px × 1.7 × 3)       |

> **Note :** `line-clamp` tronque nativement avec `…` en fin de dernière ligne visible. Pas besoin de pseudo-élément personnalisé.

Ce CSS doit être ajouté dans `carousel.css` (composant carousel) plutôt que dans `testimony.css` (composant carte) puisque c'est spécifique au contexte carousel.

### 2. Lien « Voir plus » — PHP

Ajouter un lien « Voir plus » **après** la zone `.testimony-card__quote`, visible uniquement quand le contenu est tronqué. Le lien sera masqué/affiché par JS selon que le contenu déborde ou non.

**Modification de `template-parts/content-testimony.php` :**

Ajouter après `</div><!-- .testimony-card__quote -->` et avant `<footer>` :

```php
<button
	class="testimony-card__toggle"
	aria-expanded="false"
	hidden
>
	<?php esc_html_e( 'Voir plus', 'kiyose' ); ?>
</button>
```

**Détails :**

- Élément `<button>` (pas un lien `<a>`) car il déclenche une action in-page, pas une navigation
- `hidden` par défaut (rendu serveur) : JS le retire si le contenu déborde effectivement
- `aria-expanded="false"` pour l'état initial (basculé par JS)

### 3. Détection du débordement — JS

Créer un nouveau module `assets/js/modules/testimony-expand.js` qui :

1. **Au chargement + au resize** : pour chaque `.carousel__slide .testimony-card__quote`, compare `scrollHeight` vs `clientHeight`
2. **Si déborde** : affiche le `<button>` `.testimony-card__toggle` adjacent (retire l'attribut `hidden`)
3. **Si ne déborde pas** : masque le bouton (`hidden`)

```js
function checkOverflow(quoteEl) {
	const toggle = quoteEl.closest('.testimony-card').querySelector('.testimony-card__toggle');
	if (!toggle) return;

	if (quoteEl.scrollHeight > quoteEl.clientHeight) {
		toggle.hidden = false;
	} else {
		toggle.hidden = true;
		toggle.setAttribute('aria-expanded', 'false');
	}
}
```

Appeler `checkOverflow` sur chaque `.carousel__slide .testimony-card__quote` :

- Au `DOMContentLoaded`
- Après chaque changement de slide (le slide actif peut avoir un contenu différent)
- Au `resize` (debounced, 250ms)

### 4. Comportement « Voir plus » — expansion overlay

Au clic sur le bouton « Voir plus » :

1. **Ajouter le modifier** `testimony-card--expanded` sur le `.testimony-card` parent
2. **Basculer** `aria-expanded="true"` sur le bouton
3. **Changer le texte** du bouton en icône de fermeture (croix)
4. **Mettre en pause** l'autoplay du carousel (empêcher le changement de slide pendant la lecture)
5. **Focus trap** : piéger le focus dans la carte dépliée

**Fermeture** (revient à l'état tronqué) :

- Clic sur le bouton croix
- Clic hors de la carte dépliée
- Touche `Escape`

À la fermeture :

1. Retirer le modifier `--expanded`
2. Basculer `aria-expanded="false"`
3. Remettre le texte « Voir plus »
4. Rendre le focus au bouton « Voir plus »
5. Ne **pas** relancer l'autoplay automatiquement (l'utilisateur l'a interrompu volontairement)

### 5. CSS de l'état déplié — `carousel.css`

L'état déplié superpose le contenu **sans décaler le reste de la page** (exigence du PRD original).

```css
/* La carte dépliée se positionne au-dessus du flux */
.testimony-card--expanded {
	position: relative;
	z-index: 10;
}

/* Retirer le line-clamp quand déplié */
.testimony-card--expanded .testimony-card__quote {
	-webkit-line-clamp: unset;
	overflow: visible;
}

/* Fond derrière la carte pour masquer le contenu en dessous */
.testimony-card--expanded::after {
	background: var(--kiyose-color-background);
	bottom: 0;
	content: '';
	left: 0;
	position: absolute;
	right: 0;
	top: 0;
	z-index: -1;
}
```

> **Approche** : la carte dépliée reste dans le flux du slide (pas de `position: fixed` ou portal) mais son contenu déborde visuellement vers le bas par-dessus les éléments suivants grâce au `z-index`. L'`::after` fournit un fond opaque.

> **Note** : il faudra peut-être ajuster `overflow: hidden` sur `.carousel` pour le rendre `overflow: visible` sur l'axe vertical quand un témoignage est déplié. Le modifier `carousel--has-expanded` sur `.carousel` pourra piloter ce changement :

```css
.carousel--has-expanded {
	overflow: visible;
}
```

### 6. Style du bouton « Voir plus » / croix — `testimony.css`

```css
.testimony-card__toggle {
	background: none;
	border: none;
	color: var(--kiyose-color-accent);
	cursor: pointer;
	font-size: var(--kiyose-font-size-sm);
	font-weight: 600;
	margin-top: var(--kiyose-spacing-sm);
	min-height: 44px;
	min-width: 44px;
	padding: var(--kiyose-spacing-xs) var(--kiyose-spacing-sm);
	text-decoration: underline;
	text-underline-offset: 2px;
}

.testimony-card__toggle:hover {
	color: var(--kiyose-color-burgundy);
}

.testimony-card__toggle:focus-visible {
	outline: 2px solid var(--kiyose-color-accent);
	outline-offset: 2px;
}
```

**État déplié (croix de fermeture)** :

```css
.testimony-card--expanded .testimony-card__toggle {
	position: absolute;
	right: var(--kiyose-spacing-sm);
	top: var(--kiyose-spacing-sm);
}
```

Le contenu du bouton en état déplié sera une icône SVG croix (inline, `aria-hidden="true"`) + un `aria-label` « Fermer ».

### 7. Interaction avec le carousel JS

Le module `testimony-expand.js` doit interagir avec `KiyoseCarousel` :

- **Obtenir l'instance carousel** : le carousel stocke déjà son instance via `data-carousel-initialized`. Ajouter `element._kiyoseCarousel = this` dans `init()` pour permettre l'accès depuis l'extérieur.
- **À l'expansion** : appeler `carousel.stopAutoplay()` et désactiver les boutons prev/next (prévenir le changement de slide quand une carte est dépliée)
- **À la fermeture** : réactiver les boutons prev/next. Ne pas relancer l'autoplay.

### 8. Chargement JS — `main.js`

Le module `testimony-expand.js` est chargé comme ES module import depuis `main.js`, en cohérence avec l'architecture existante (`carousel.js` est importé de la même manière). Pas d'enqueue séparé via `inc/enqueue.php`.

```js
import { TestimonyExpand } from './modules/testimony-expand.js';

// Dans init(), après l'initialisation des carousels :
const testimonyExpand = new TestimonyExpand();
testimonyExpand.init();
```

> **Note** : `carousel.js` n'est pas enqueué séparément — il est importé statiquement par `main.js` (chargé en `type="module"`). `testimony-expand.js` suit le même pattern pour la cohérence architecturale.

## Périmètre

Ce comportement s'applique à **tous les carousels de témoignages** :

- Section témoignages de la page d'accueil (`templates/page-home.php`)
- Shortcode `[kiyose_testimonials display="carousel"]` (`inc/shortcodes.php`)

**Hors périmètre :**

- La grille de témoignages (shortcode `display="grid"`) n'est pas concernée
- Pas de redesign visuel de la carte (hors ajout du bouton)
- Pas de modification du nombre de témoignages affichés

## Accessibilité

| Critère                  | Spécification                                                                                                                                                                         |
| ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Bouton sémantique        | `<button>` natif, pas un `<a>` ou `<div>`                                                                                                                                             |
| État annoncé             | `aria-expanded="false"` / `"true"` sur le bouton                                                                                                                                      |
| Nom accessible (replié)  | Texte visible « Voir plus »                                                                                                                                                           |
| Nom accessible (déplié)  | `aria-label="Fermer"` + icône SVG `aria-hidden="true"`                                                                                                                                |
| Fermeture clavier        | `Escape` ferme le bloc déplié                                                                                                                                                         |
| Retour du focus          | Focus rendu au bouton « Voir plus » après fermeture                                                                                                                                   |
| Focus trap               | Focus piégé dans la carte dépliée (ne pas tabuler hors de la carte)                                                                                                                   |
| Cible tactile            | Bouton ≥ 44×44px (WCAG 2.2 AA)                                                                                                                                                        |
| Contraste                | Texte accent `#E6A528` sur fond blanc `#FFFFFF` → ratio 2.14:1. **Insuffisant.** Utiliser `--kiyose-color-burgundy` (`#5D0505`) sur blanc → ratio 12.35:1 pour le lien « Voir plus ». |
| `prefers-reduced-motion` | L'expansion/fermeture ne doit pas être animée si `prefers-reduced-motion: reduce`                                                                                                     |
| Masquage conditionnel    | `hidden` sur le bouton quand le contenu ne déborde pas → invisible au DOM, pas tabulable                                                                                              |

## Fichiers à créer / modifier

### Nouveaux fichiers

| Fichier                                     | Description                                            |
| ------------------------------------------- | ------------------------------------------------------ |
| `assets/js/modules/testimony-expand.js`     | Module de détection de débordement + expansion overlay |
| `assets/js/modules/testimony-expand.min.js` | Version minifiée                                       |

### Fichiers modifiés

| Fichier                                   | Modification                                                                                              |
| ----------------------------------------- | --------------------------------------------------------------------------------------------------------- |
| `template-parts/content-testimony.php`    | Ajout du `<button class="testimony-card__toggle">` entre `.testimony-card__quote` et `<footer>`           |
| `assets/css/components/carousel.css`      | Ajout du `line-clamp` sur `.carousel__slide .testimony-card__quote` + styles expansion                    |
| `assets/css/components/carousel.min.css`  | Régénérer                                                                                                 |
| `assets/css/components/testimony.css`     | Styles du bouton `.testimony-card__toggle`                                                                |
| `assets/css/components/testimony.min.css` | Régénérer                                                                                                 |
| `assets/js/modules/carousel.js`           | Exposer l'instance via `element._kiyoseCarousel = this` + méthodes `disableNavigation`/`enableNavigation` |
| `assets/js/modules/carousel.min.js`       | Régénérer                                                                                                 |
| `assets/js/main.js`                       | Import et initialisation de `TestimonyExpand`                                                             |
| `assets/js/main.min.js`                   | Régénérer                                                                                                 |

## Tests et validation

### Visuel

- [ ] Tous les témoignages dans le carousel ont la même hauteur visible (pas d'espace vide)
- [ ] Les citations courtes (≤ 3 lignes) n'affichent pas le bouton « Voir plus »
- [ ] Les citations longues (> 3 lignes) affichent « Voir plus » et se terminent par `…`
- [ ] Au clic sur « Voir plus », la citation complète apparaît en overlay sans décaler la page
- [ ] Le fond de la carte dépliée masque le contenu en dessous
- [ ] La croix de fermeture est visible et bien positionnée
- [ ] Rendu cohérent sur Chrome, Firefox, Safari (macOS, iOS), Android Chrome
- [ ] Responsive correct à 320px, 768px, 1024px, 1280px

### Accessibilité

- [ ] Bouton « Voir plus » atteignable au Tab et activable avec Entrée/Espace
- [ ] `aria-expanded` bascule correctement entre `false` et `true`
- [ ] Lecteur d'écran (VoiceOver) annonce « Voir plus, bouton réduit » / « Fermer, bouton développé »
- [ ] `Escape` ferme le bloc déplié
- [ ] Focus revient au bouton « Voir plus » après fermeture
- [ ] Focus trap : Tab/Shift+Tab ne sort pas de la carte dépliée
- [ ] Contraste du bouton ≥ 4.5:1
- [ ] Cible tactile ≥ 44×44px
- [ ] `prefers-reduced-motion: reduce` : pas d'animation à l'expansion/fermeture
- [ ] Bouton masqué (`hidden`) quand le contenu ne déborde pas → pas tabulable

### Fonctionnel

- [ ] L'autoplay du carousel se met en pause quand une citation est dépliée
- [ ] Les boutons prev/next sont désactivés quand une citation est dépliée
- [ ] Clic hors de la carte dépliée la referme
- [ ] L'autoplay ne reprend pas automatiquement après fermeture
- [ ] Le shortcode `[kiyose_testimonials display="carousel"]` a le même comportement
- [ ] La grille `[kiyose_testimonials display="grid"]` n'est pas impactée
- [ ] Resize de la fenêtre recalcule correctement le débordement

### Standards

- [ ] `make test` passe (PHPCS, ESLint, Stylelint)
- [ ] Variables CSS préfixées `--kiyose-`
- [ ] Classes BEM cohérentes (`testimony-card__toggle`, `testimony-card--expanded`)
- [ ] `line-clamp` compatible navigateurs cibles (Chrome 60+, Firefox 68+, Safari 12+)
