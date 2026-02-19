# PRD 0020 — Éléments décoratifs "Collage Créatif"

## Statut

**brouillon**

## Objectif

Remplacer les éléments décoratifs actuels (bulles SVG circulaires, overlays vitrail) par un système visuel inspiré du Journal Créatif et du collage — papiers déchirés, traces brutes, micro-éléments griffonnés — qui exprime l'énergie joyeuse et l'esprit "fait main" de l'atelier, sans évoquer un atelier de peinture.

## Contexte

Le site possède actuellement trois types d'éléments décoratifs Kintsugi :
- **Section dividers dorés** : Lignes dorées entre les sections (conservés)
- **Overlays vitrail** : Gradients semi-transparents sur le hero et certaines sections
- **Bulles SVG** : Cercles roses sur les cartes services, visibles uniquement au hover

**Problème** : Les bulles sont plates, monochromes et peu expressives. L'ensemble manque de vivacité et de couleur. Le site doit mieux refléter l'énergie joyeuse et créative de L'Atelier Kiyose — art-thérapie, Journal Créatif, rigologie — sans glisser vers un univers "beaux-arts" ou "atelier de peintre".

**Solution** : Un langage visuel inspiré du collage et du mixed media — des morceaux de papier déchiré assemblés et réparés à l'or (kintsugi sur papier) — combiné à des micro-éléments bruts mais stylisés (gribouillis, petites formes découpées). L'ensemble évoque un journal créatif soigné : brouillon dans l'esprit, travaillé dans l'exécution.

## Dépendances

- **PRD 0002** : Design tokens (variables CSS)
- **PRD 0008** : Page d'accueil (structure HTML des sections)
- **PRD 0014** : Kintsugi et finitions visuelles (section dividers dorés conservés)
- **PRD 0018** : Animations de la page d'accueil (pattern Intersection Observer réutilisé)

## Concept : système décoratif en 3 couches

1. **Papiers déchirés** (couche fond) — Grandes formes semi-transparentes aux bords irréguliers en arrière-plan des sections, comme des morceaux de papier collés
2. **Lignes dorées Kintsugi** (couche intermédiaire) — Les section dividers existants, recontextualisés comme la colle dorée qui relie les morceaux entre eux
3. **Micro-éléments mixtes** (couche détail) — Petites formes révélées au scroll : gribouillis, traits, formes découpées, comme les annotations d'un journal créatif

### Métaphore centrale

Le kintsugi appliqué au papier : des morceaux de papier de différentes teintes, assemblés et réparés avec de l'or. Les fêlures entre les morceaux laissent passer la lumière dorée. C'est du collage, pas de la peinture.

### Palette enrichie

Deux nouvelles couleurs d'accent pour les éléments décoratifs :

| Couleur | Variable CSS | Hex | Usage |
|---------|-------------|-----|-------|
| Rose poudré (existant) | `--kiyose-color-primary` | `#D7A4A4` | Papiers déchirés principaux |
| Or miel (existant) | `--kiyose-color-accent` | `#E6A528` | Lignes Kintsugi entre les morceaux |
| **Corail chaud** (nouveau) | `--kiyose-color-coral` | `#E07A5F` | Papiers secondaires — énergie, joie |
| **Turquoise doux** (nouveau) | `--kiyose-color-turquoise` | `#7EB8B0` | Micro-éléments — fraîcheur, créativité |

> Les nouvelles couleurs sont réservées aux éléments décoratifs. Elles ne sont pas utilisées pour les composants UI (boutons, liens, texte).

## Spécifications

### 1. Papiers déchirés (fond de sections)

**Principe** : Des formes qui évoquent des morceaux de papier collés — bords dentelés et irréguliers (pas de courbes lisses), légère rotation, superpositions partielles entre morceaux adjacents.

**Technique** : SVG inline avec `<path>` aux bords dentelés (segments courts avec micro-variations aléatoires, pas de courbes de Bézier lisses). Fill uni ou léger dégradé linéaire (pas de dégradé radial type aquarelle). Pas de filtre flou — les bords restent nets pour garder l'effet "découpé".

**Caractéristiques visuelles d'un morceau de papier** :
- **Bords** : Irréguliers, dentelés, comme du papier arraché (segments de 5-15px avec des variations verticales de ±3-8px)
- **Fill** : Couleur unie semi-transparente, éventuellement un très léger dégradé linéaire pour simuler l'épaisseur du papier
- **Rotation** : Légère inclinaison (-5° à +5°) via `transform: rotate()` pour l'effet "collé à la main"
- **Pas de flou** : Bords nets (pas de `feGaussianBlur`)

**Placement sur la page d'accueil** :

| Section | Position | Couleur | Opacité | Taille approx. | Rotation |
|---------|----------|---------|---------|----------------|----------|
| Hero | Grand morceau asymétrique, côté droit | Rose poudré | 12-15% | ~400×300px | -3° |
| Hero | Petit morceau, coin bas-gauche | Turquoise | 8-10% | ~180×150px | +4° |
| Services | Morceau débordant en haut à gauche | Corail | 8-12% | ~300×220px | +2° |
| Événements | Morceau côté droit | Turquoise | 8-10% | ~250×280px | -4° |
| Témoignages | Morceau coin haut-droit | Rose poudré | 10% | ~200×180px | +3° |
| Blog | Morceau bas-gauche | Corail | 8% | ~230×200px | -2° |

**Propriétés CSS communes** :

```css
.collage-paper {
	position: absolute;
	pointer-events: none;
	z-index: 0;
}
```

**Responsive** :
- Mobile (< 768px) : Tailles réduites de 40%, opacités réduites de 30%
- Masquer les morceaux secondaires si ils gênent la lisibilité sur petit écran

### 2. Lignes dorées Kintsugi (couche intermédiaire)

**Principe** : Les section dividers dorés existants (PRD 0014) sont conservés tels quels. Ils prennent un nouveau sens dans le contexte collage : ce sont les joints d'or qui relient les morceaux de papier entre eux.

**Ajustement de l'overlay** : L'overlay vitrail actuel (`.hero-section--with-overlay::before`) est remplacé par de subtils halos dorés positionnés aux "jointures" entre les morceaux de papier, comme si la lumière filtrait entre les bords déchirés.

**Hero** (remplace l'overlay vitrail actuel dans `.hero-section--with-overlay::before`) :

```css
.hero-section--with-overlay::before {
	background:
		radial-gradient(ellipse at 65% 40%, rgb(230 165 40 / 10%) 0%, transparent 35%),
		radial-gradient(ellipse at 25% 60%, rgb(244 201 117 / 6%) 0%, transparent 30%);
}
```

**Sections alternées** : Petits halos dorés positionnés près des bords des papiers déchirés (ajustement du `.section--with-overlay::after` existant). Opacité réduite par rapport à la version précédente (6-10% au lieu de 8-12%) pour laisser les papiers dominer visuellement.

### 3. Micro-éléments mixtes (scroll reveal)

**Principe** : Petits éléments évoquant les annotations, gribouillis et petites formes découpées qu'on trouve dans un journal créatif. Variété de formes pour casser la répétitivité.

**Catalogue de micro-éléments** (SVG, 30-60px) :

| Type | Description | Technique SVG |
|------|-------------|---------------|
| **Gribouillis spirale** | Petit tourbillon dessiné à la main | `<path>` spirale avec traits irréguliers, `stroke` sans `fill` |
| **Trait de crayon** | Ligne courte, légèrement ondulée | `<path>` avec 2-3 segments irréguliers, `stroke-width: 2-3px` |
| **Petit cercle imparfait** | Cercle pas tout à fait fermé, tremblé | `<path>` pseudo-circulaire (pas de `<circle>`), `stroke` sans `fill` |
| **Forme découpée** | Petit triangle ou étoile aux bords dentelés | `<path>` avec bords irréguliers, `fill` uni |
| **Croix / astérisque** | Petite marque d'annotation | `<path>` deux traits croisés, `stroke` épais |
| **Point d'encre** | Petite tache irrégulière | `<path>` forme blob, `fill` uni |

**Comportement** :
- Initialement invisibles (`opacity: 0`)
- Fade-in quand leur section entre dans le viewport (300ms)
- Décalage temporel entre chaque élément (effet cascade, 100ms entre chaque)
- **`prefers-reduced-motion: reduce`** : Apparition instantanée, pas de transition

**Quantité** : 2-3 micro-éléments par section, positionnés aux marges.

**Couleurs** : Alternance entre rose, or, corail, turquoise. Les gribouillis et traits utilisent `stroke` (pas de fill) pour un rendu "dessiné". Les formes découpées et taches utilisent `fill` pour un rendu "collé".

**Implémentation** : Nouveau module `decorative-reveal.js` (pas de surcharge de `home-animations.js`). Réutilise le même pattern : Intersection Observer + `requestAnimationFrame` + respect de `prefers-reduced-motion`.

## Direction artistique — ce qu'il faut éviter

| Éviter | Pourquoi | Faire plutôt |
|--------|----------|--------------|
| Courbes de Bézier lisses | Trop "aquarelle" / beaux-arts | Segments courts, dentelés, irréguliers |
| Dégradés radiaux sur les fonds | Trop "tache de peinture" | Couleurs unies ou dégradés linéaires subtils |
| Filtres flou (`feGaussianBlur`) | Trop doux, perd l'effet "découpé" | Bords nets, texture papier |
| Formes symétriques | Trop propre, pas assez "fait main" | Formes asymétriques, légèrement de travers |
| Trop de superpositions fluides | Effet aquarelle qui se mélange | Superpositions nettes, bords qui se chevauchent |

## Fichiers impactés

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `assets/css/variables.css` | Ajouter `--kiyose-color-coral` et `--kiyose-color-turquoise` |
| `assets/css/components/kintsugi.css` | Remplacer la section "DECORATIVE BUBBLES" par le système collage + ajuster les overlays |
| `template-parts/content-service.php` | Supprimer les SVG `<circle>` des bulles (lignes 32-38) |
| `templates/page-home.php` | Ajouter les papiers déchirés SVG et micro-éléments dans les sections |
| `inc/enqueue.php` | Enregistrer `decorative-reveal.js` |
| `doc/design-system.md` | Documenter les nouvelles couleurs et le système décoratif collage |

### Fichiers créés

| Fichier | Contenu |
|---------|---------|
| `assets/js/modules/decorative-reveal.js` | Module Intersection Observer pour le scroll reveal des micro-éléments |
| `template-parts/decorative-collage.php` | Template part réutilisable pour les papiers déchirés SVG (paramétrable : couleur, taille, position, rotation) |

### Fichiers supprimés (code supprimé)

| Code | Fichier |
|------|---------|
| SVG `<circle>` des bulles (lignes 32-38) | `template-parts/content-service.php` |
| Styles `.bubble`, `.bubble--1`, `.bubble--2` (lignes 193-250) | `assets/css/components/kintsugi.css` |

## Éléments conservés

- **Section dividers dorés** : Inchangés (recontextualisés comme "joints Kintsugi entre morceaux")
- **Backgrounds alternés** (`.section--alt-bg`, `.section--tinted`) : Inchangés
- **Animations de cartes services** (`home-animations.js`) : Inchangées

## Accessibilité (WCAG 2.2 AA)

- Tous les éléments : `aria-hidden="true"` et `role="presentation"`
- `pointer-events: none` sur tous les éléments décoratifs
- `@media (prefers-reduced-motion: reduce)` : Pas de transitions, apparition immédiate
- Opacités basses (8-15%) pour ne jamais gêner la lisibilité
- Pas de mouvement automatique ni continu
- Vérifier le contraste texte/fond avec les papiers en arrière-plan

## Performance

- SVG inline : Pas de requêtes HTTP supplémentaires
- CSS gradients pour les halos : Coût GPU minimal
- Intersection Observer pour le scroll reveal : Pas d'écouteurs scroll coûteux pour les micro-éléments
- `will-change: opacity` uniquement pendant la transition, retiré après
- Mobile : Réduction du nombre d'éléments décoratifs

## Vérification

1. **Rendu visuel** : `make start` → naviguer sur `http://127.0.0.1:8000` → vérifier les 3 couches décoratives sur toutes les sections
2. **Cohérence collage** : L'ensemble évoque un journal créatif / collage, PAS un atelier de peinture
3. **Responsive** : Tester à 320px, 768px, 1024px, 1280px — les éléments s'adaptent sans gêner la lecture
4. **Accessibilité** : Activer `prefers-reduced-motion: reduce` dans DevTools → vérifier l'absence de transitions
5. **Navigation clavier** : Vérifier que les éléments décoratifs n'interceptent pas le focus (Tab)
6. **PHPCS** : `make phpcs` → aucune violation
7. **Lint JS** : ESLint sur `decorative-reveal.js`
8. **Performance** : Lighthouse → vérifier que LCP n'est pas impacté
