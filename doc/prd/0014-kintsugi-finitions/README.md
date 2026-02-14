# PRD 0014 — Kintsugi et finitions visuelles

## Objectif

Ajouter les éléments décoratifs de l'identité visuelle Kintsugi (séparateurs dorés, overlays vitrail, bulles décoratives) et finaliser les animations/transitions du site.

## Dépendances

- **PRD 0002** : Variables CSS et palette en place.
- **PRD 0003** : Layout et composants de base.

## Contexte

L'identité visuelle Kintsugi est décrite dans `doc/design-system.md` (section "Identité visuelle Kintsugi"). Les implémentations CSS de référence sont dans `references/css-examples.css`.

Ce PRD est volontairement en fin de parcours : ce sont des finitions visuelles qui peuvent être ajustées une fois que tout le contenu est en place.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
└── assets/
    └── css/
        └── components/
            ├── kintsugi.css           # Éléments décoratifs Kintsugi
            └── animations.css         # Animations et transitions globales
```

### Fichiers modifiés

- Templates divers : ajout de classes CSS pour les séparateurs et overlays
- `inc/enqueue.php` : charger les nouveaux fichiers CSS

## Spécifications détaillées

### 1. Séparateurs dorés (section dividers)

Lignes dorées subtiles entre les sections, inspirées des fissures réparées à l'or.

**Implémentation** : Reprendre les styles de `references/css-examples.css` (`.section-divider`, `.section-divider--organic`)

**Utilisation** :
- Entre les sections de la homepage (hero → services → événements → etc.)
- Optionnellement entre les sections d'autres pages

**HTML** :
```html
<div class="section-divider" role="presentation" aria-hidden="true"></div>
```

**Points clés** :
- `role="presentation"` et `aria-hidden="true"` : purement décoratif
- Couleur : `--color-gold` (#E6A528)
- Dégradé : opaque au centre, transparent aux bords
- Marge verticale : `--spacing-3xl` (4rem)

### 2. Overlays vitrail

Overlays semi-transparents évoquant la métaphore du vitrail ("l'être humain comme un vitrail").

**Implémentation** : Reprendre les styles de `references/css-examples.css` (`.hero-section::before`, `.section--with-overlay::after`)

**Utilisation** :
- Section hero de la homepage
- Sections alternées (fond avec légère teinte colorée)

**Contrainte** : L'opacité doit être très faible (0.05-0.15) pour ne pas nuire à la lisibilité du contenu.

### 3. Bulles décoratives

Bulles SVG apparaissant au survol/focus d'éléments interactifs (cartes de services, boutons).

**Implémentation** : Reprendre les styles de `references/css-examples.css` (`.bubble`)

**Comportement** :
- Pas d'animation automatique (conformité WCAG 2.2 AA)
- Apparition au survol/focus uniquement
- Fade-in 300ms, fade-out 200ms
- Couleurs : rose poudré, taupe rosé, opacité 0.6-0.8
- Ne jamais chevaucher du contenu important

**HTML** :
```html
<div class="service-card">
    <!-- Contenu de la carte -->
    <svg class="bubble bubble--1" aria-hidden="true">
        <circle cx="20" cy="20" r="15" fill="var(--color-primary)" opacity="0.7"/>
    </svg>
</div>
```

**`aria-hidden="true"`** : Les bulles sont purement décoratives.

### 4. Animations et transitions globales

Reprendre les styles de `references/css-examples.css` (section ANIMATIONS & TRANSITIONS) :

- **Boutons** : `translateY(-2px)` au hover, transition 200ms
- **Cartes** : `translateY(-4px)` + ombre accrue au hover, transition 300ms
- **Liens** : transition de couleur 200ms

**Toutes les animations respectent `prefers-reduced-motion`** :
```css
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

### 5. Sections alternées

Alternance de fond entre sections (beige clair / blanc / taupe rosé léger) pour créer un rythme visuel :

```css
.section--alt-bg {
    background-color: white;
}

.section--tinted {
    background-color: rgba(201, 171, 167, 0.1); /* Taupe rosé très léger */
}
```

## Tests et validation

### Tests manuels — Visuels
- [ ] Séparateurs dorés visibles entre les sections
- [ ] Overlays vitrail subtils sur le hero et sections concernées
- [ ] Bulles décoratives apparaissent au survol des cartes de services
- [ ] Transitions fluides sur les boutons et cartes
- [ ] Alternance de couleurs de fond entre sections

### Tests manuels — Accessibilité
- [ ] Tous les éléments décoratifs ont `aria-hidden="true"`
- [ ] Aucune information transmise uniquement par la couleur ou la décoration
- [ ] Contraste texte/fond maintenu sur toutes les sections (y compris avec overlays)
- [ ] `prefers-reduced-motion: reduce` désactive toutes les animations
- [ ] Les bulles ne chevauchent pas de contenu important

### Tests manuels — Performance
- [ ] Les animations ne causent pas de jank (utiliser transform/opacity uniquement)
- [ ] Pas de CLS dû aux éléments décoratifs
- [ ] Les SVG sont légers

## Hors périmètre

- Refonte du design des composants existants
- Nouvelles fonctionnalités
