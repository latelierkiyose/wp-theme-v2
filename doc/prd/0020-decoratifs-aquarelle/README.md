# PRD 0020 — Éléments décoratifs "Atelier Baigné de Lumière"

## Statut

**brouillon**

## Objectif

Remplacer les éléments décoratifs actuels (bulles SVG circulaires, overlays vitrail) par un système visuel organique et fluide — taches d'aquarelle et éclats de lumière dorée — qui exprime la joie, la créativité et le dynamisme de l'atelier.

## Contexte

Le site possède actuellement trois types d'éléments décoratifs Kintsugi :
- **Section dividers dorés** : Lignes dorées entre les sections (conservés)
- **Overlays vitrail** : Gradients semi-transparents sur le hero et certaines sections
- **Bulles SVG** : Cercles roses sur les cartes services, visibles uniquement au hover

**Problème** : Les bulles sont plates, monochrones et peu expressives. L'ensemble manque de vivacité et de couleur. Le site doit mieux refléter l'énergie joyeuse et créative de L'Atelier Kiyose.

**Solution** : Un langage visuel inspiré de l'atelier d'artiste — des taches d'aquarelle organiques et des éclats de lumière traversant les œuvres — cohérent avec la métaphore Kintsugi tout en étant plus coloré et dynamique.

## Dépendances

- **PRD 0002** : Design tokens (variables CSS)
- **PRD 0008** : Page d'accueil (structure HTML des sections)
- **PRD 0014** : Kintsugi et finitions visuelles (section dividers dorés conservés)
- **PRD 0018** : Animations de la page d'accueil (pattern Intersection Observer réutilisé)

## Concept : système décoratif en 3 couches

1. **Taches d'aquarelle** (couche fond) — Grandes formes organiques semi-transparentes en arrière-plan des sections
2. **Éclats de lumière dorée** (couche intermédiaire) — Halos Kintsugi qui traversent les taches, comme la lumière à travers un vitrail
3. **Micro-éclaboussures** (couche détail) — Petites touches colorées révélées au scroll

### Palette enrichie

Deux nouvelles couleurs d'accent pour les éléments décoratifs :

| Couleur | Variable CSS | Hex | Usage |
|---------|-------------|-----|-------|
| Rose poudré (existant) | `--kiyose-color-primary` | `#D7A4A4` | Taches aquarelle principales |
| Or miel (existant) | `--kiyose-color-accent` | `#E6A528` | Éclats de lumière, halos |
| **Corail chaud** (nouveau) | `--kiyose-color-coral` | `#E07A5F` | Taches secondaires — énergie, joie |
| **Turquoise doux** (nouveau) | `--kiyose-color-turquoise` | `#7EB8B0` | Touches d'accent — fraîcheur, créativité |

> Les nouvelles couleurs sont réservées aux éléments décoratifs. Elles ne sont pas utilisées pour les composants UI (boutons, liens, texte).

## Spécifications

### 1. Taches d'aquarelle (fond de sections)

**Technique** : SVG inline avec `<path>` organiques (courbes de Bézier irrégulières) + dégradés radiaux dans le fill SVG + filtre flou (`<filter>` SVG avec `feGaussianBlur`, sigma 8-15px).

**Placement sur la page d'accueil** :

| Section | Position | Couleurs | Opacité | Taille approx. |
|---------|----------|----------|---------|----------------|
| Hero | Grande tache asymétrique, côté droit | Rose poudré → Corail | 12-18% | ~400×350px |
| Hero | Petite tache, coin bas-gauche | Turquoise | 10% | ~200×180px |
| Services | Tache débordant en haut à gauche | Corail → Or | 8-12% | ~300×250px |
| Événements | Tache côté droit | Turquoise → Rose | 8-10% | ~250×300px |
| Témoignages | Tache coin haut-droit | Rose → Or | 10% | ~200×200px |
| Blog | Tache bas-gauche | Corail → Turquoise | 8% | ~250×200px |

**Propriétés CSS communes** :

```css
.watercolor-splash {
	position: absolute;
	pointer-events: none;
	z-index: 0;
}
```

**Responsive** :
- Mobile (< 768px) : Tailles réduites de 40%, opacités réduites de 30%
- Masquer les taches secondaires si elles gênent la lisibilité sur petit écran

### 2. Éclats de lumière dorée (halos Kintsugi)

**Technique** : CSS `radial-gradient`, extension du pattern existant `.hero-section--with-overlay` et `.section--with-overlay`.

Les halos se superposent aux taches d'aquarelle, créant un effet de lumière traversant une toile peinte.

**Hero** (remplace l'overlay vitrail actuel dans `.hero-section--with-overlay::before`) :

```css
.hero-section--with-overlay::before {
	background:
		radial-gradient(ellipse at 65% 40%, rgb(230 165 40 / 12%) 0%, transparent 45%),
		radial-gradient(ellipse at 25% 60%, rgb(244 201 117 / 8%) 0%, transparent 40%);
}
```

**Sections alternées** : Petits halos dorés positionnés près des taches aquarelle (ajustement du `.section--with-overlay::after` existant).

### 3. Micro-éclaboussures (scroll reveal)

**Technique** : Petits SVG (30-60px) révélés via Intersection Observer.

**Comportement** :
- Initialement invisibles (`opacity: 0`)
- Fade-in quand leur section entre dans le viewport (300ms)
- Décalage temporel entre chaque éclaboussure (effet cascade, 100ms entre chaque)
- **`prefers-reduced-motion: reduce`** : Apparition instantanée, pas de transition

**Quantité** : 2-3 micro-éclaboussures par section, positionnées aux marges.

**Couleurs variées** : Alternance entre rose, or, corail, turquoise.

**Implémentation** : Nouveau module `decorative-reveal.js` (pas de surcharge de `home-animations.js`). Réutilise le même pattern : Intersection Observer + `requestAnimationFrame` + respect de `prefers-reduced-motion`.

## Fichiers impactés

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `assets/css/variables.css` | Ajouter `--kiyose-color-coral` et `--kiyose-color-turquoise` |
| `assets/css/components/kintsugi.css` | Remplacer la section "DECORATIVE BUBBLES" par le système aquarelle + ajuster les overlays |
| `template-parts/content-service.php` | Supprimer les SVG `<circle>` des bulles (lignes 32-38) |
| `templates/page-home.php` | Ajouter les taches SVG aquarelle et micro-éclaboussures dans les sections |
| `inc/enqueue.php` | Enregistrer `decorative-reveal.js` |
| `doc/design-system.md` | Documenter les nouvelles couleurs et le système décoratif |

### Fichiers créés

| Fichier | Contenu |
|---------|---------|
| `assets/js/modules/decorative-reveal.js` | Module Intersection Observer pour le scroll reveal |
| `template-parts/decorative-watercolor.php` | Template part réutilisable pour les taches SVG (paramétrable : couleur, taille, position) |

### Fichiers supprimés (code supprimé)

| Code | Fichier |
|------|---------|
| SVG `<circle>` des bulles (lignes 32-38) | `template-parts/content-service.php` |
| Styles `.bubble`, `.bubble--1`, `.bubble--2` (lignes 193-250) | `assets/css/components/kintsugi.css` |

## Éléments conservés

- **Section dividers dorés** : Inchangés
- **Backgrounds alternés** (`.section--alt-bg`, `.section--tinted`) : Inchangés
- **Animations de cartes services** (`home-animations.js`) : Inchangées

## Accessibilité (WCAG 2.2 AA)

- Tous les éléments : `aria-hidden="true"` et `role="presentation"`
- `pointer-events: none` sur tous les éléments décoratifs
- `@media (prefers-reduced-motion: reduce)` : Pas de transitions, apparition immédiate
- Opacités basses (8-18%) pour ne jamais gêner la lisibilité
- Pas de mouvement automatique ni continu
- Vérifier le contraste texte/fond avec les taches en arrière-plan

## Performance

- SVG inline : Pas de requêtes HTTP supplémentaires
- CSS gradients pour les halos : Coût GPU minimal
- Intersection Observer pour le scroll reveal : Pas d'écouteurs scroll coûteux pour les micro-éclaboussures
- `will-change: opacity` uniquement pendant la transition, retiré après
- Mobile : Réduction du nombre d'éléments décoratifs

## Vérification

1. **Rendu visuel** : `make start` → naviguer sur `http://127.0.0.1:8000` → vérifier les 3 couches décoratives sur toutes les sections
2. **Responsive** : Tester à 320px, 768px, 1024px, 1280px — les éléments s'adaptent sans gêner la lecture
3. **Accessibilité** : Activer `prefers-reduced-motion: reduce` dans DevTools → vérifier l'absence de transitions
4. **Navigation clavier** : Vérifier que les éléments décoratifs n'interceptent pas le focus (Tab)
5. **PHPCS** : `make phpcs` → aucune violation
6. **Lint JS** : ESLint sur `decorative-reveal.js`
7. **Performance** : Lighthouse → vérifier que LCP n'est pas impacté
