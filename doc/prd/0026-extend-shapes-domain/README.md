# PRD 0026 — Extension de la surface couverte par les shapes

**Statut** : terminé

## Contexte

Les shapes décoratives (splashes, blobs, brushstrokes, spirals) sont actuellement
positionnées exclusivement dans les marges latérales en desktop (10 shapes en `margin-left`
/ `margin-right`, à `left: -3%` / `right: -3%`).

Sur mobile (< 768px), le viewport étroit fait que ces mêmes shapes débordent naturellement
sous le contenu, ce qui donne un effet visuel agréable.

L'objectif est de reproduire cet effet en desktop en ajoutant des shapes sous le contenu,
avec une densité et une opacité moindres que dans les marges.

> **Note** : le type `wave` a été supprimé du codebase (forme rectangulaire jugée peu adaptée
> au style organique Kintsugi). Les shapes wave existantes ont été remplacées aléatoirement
> par `blob` ou `brushstroke`.

## Spécifications fonctionnelles

### Nouveau positionnement : `content-left` / `content-right`

- Nouveau type de position pour les shapes dans le conteneur `.deco-page-shapes`
- Positionnement horizontal : ~22% depuis le bord (sous le contenu, pas dans les marges)
- Positionnement vertical : via `--kiyose-deco-top` comme les shapes margin existantes

### Densité et nombre

- **12 shapes content** réparties uniformément sur la page (~8-12% d'intervalle vertical)
- Distribution : `top` de 4% à 92%, intervalles réguliers
- Alternance `sm` / `md` / `lg` pour varier la densité visuelle

### Opacité

- Opacité des shapes content : **0.08–0.15** (vs 0.25–0.40 pour les shapes margin)
- Cette opacité basse garantit que les shapes restent en filigrane sous le texte

### Taille

- `sm` (200px), `md` (350px) ou `lg` (500px)
- Les `sm` servent à remplir les interstices entre les `md`/`lg`

### Desktop-only

- Les shapes content sont **masquées en dessous de 768px** (`display: none`) pour ne pas
  augmenter la densité sur mobile (qui est bien comme elle est)

## Règles de positionnement

### Placement libre (y compris sous les headings)

- Les shapes content peuvent être positionnées à n'importe quel niveau vertical,
  **y compris sous les titres** de section (h1, h2)
- Le z-index existant (`.section__content { z-index: 1 }`) garantit la lisibilité
- Viser une distribution uniforme sur toute la hauteur de la page (~8-12% d'intervalle)

### Espacement avec les scribbles

- Les shapes content sont **automatiquement écartées des scribbles** via `deco-overlap.js`
- Le module gère également les collisions shape-content vs shape-margin

## Contraintes

- **Lisibilité** : le texte reste parfaitement lisible grâce au z-index existant
  (`.section__content { z-index: 1 }`) et à l'opacité très basse (0.08–0.15)
- **WCAG 2.2 AA** : pas d'impact sur le contraste texte (shapes sous le contenu,
  semi-transparentes, ne modifient pas le contraste calculé)
- **Aucun changement mobile** : les shapes content sont masquées via media query
- **Aucune modification** des shapes margin existantes (positions, opacités, tailles)
- **Aucune modification** du système de scribbles

## Interaction avec les systèmes existants

- `deco-overlap.js` : **enrichi** avec un système de priorité à 3 niveaux :
  - Priorité 0 — shapes margin (immobiles)
  - Priorité 1 — shapes content (nudgées par les margin shapes et entre elles)
  - Priorité 2 — scribbles (les plus mobiles, nudgés par tout)
- `decorative-reveal.js` : les shapes content bénéficient du parallax existant
  (même classe `.deco-shape`)

## Critères d'acceptation

- [x] Shapes content apparaissent sous le contenu en desktop (>= 768px)
- [x] Ces shapes ne sont pas visibles sur mobile (< 768px)
- [x] L'opacité des shapes content (0.08–0.15) est visuellement plus faible que les shapes margin
- [x] Le texte reste parfaitement lisible par-dessus les shapes
- [x] Les shapes content sont écartées des scribbles existants (géré par `deco-overlap.js`)
- [x] `make test` passe (PHPCS, ESLint, Stylelint)
- [x] Navigation clavier non impactée
- [x] Le parallax existant s'applique aux nouvelles shapes

## Scope

- **Homepage uniquement** (comme les shapes existantes, chargées conditionnellement dans `inc/enqueue.php`)

## Hors scope

- Ajout de shapes sur d'autres pages
- Modification de la densité/opacité des shapes margin existantes
- Modification du système de scribbles
- Ajout d'un mécanisme programmatique de densité (le placement reste manuel dans le template)
