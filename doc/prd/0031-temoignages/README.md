# PRD 0031 — Refonte de la grille de témoignages

**Statut** : terminé

## Contexte

Les témoignages affichés via le shortcode `[kiyose_testimonials]` en mode grille sont peu lisibles :
- 3 colonnes par défaut sur grand écran → texte trop étroit
- Fond jaune pâle involontaire (`.page__content blockquote` écrase le fond blanc de `.testimony-card` par spécificité CSS)
- Le séparateur entre le contenu et l'auteurice utilise `--kiyose-color-border` au lieu de la couleur dorée du thème
- La hauteur des cards est imposée par la rangée CSS Grid (les cards d'une même rangée ont toutes la même hauteur)

## Périmètre

- **Inclus** : grille du shortcode `[kiyose_testimonials display="grid"]`
- **Exclu** : carousel de la page d'accueil, carousel du shortcode

## Changements

### 1. Layout — Défaut à 2 colonnes

- Changer la valeur par défaut de `columns` de 3 à 2 dans le shortcode
- Les options 1, 3, 4 restent disponibles pour les usages personnalisés
- Breakpoint inchangé : 1 colonne en mobile, 2 colonnes à partir de 768px

### 2. Fond blanc — Corriger la spécificité CSS

Le sélecteur `.page__content blockquote` (`page.css:90`) applique `background-color: rgb(230 165 40 / 10%)` aux `<blockquote>` dans les pages de contenu. Le composant `.testimony-card` (qui est un `<blockquote>`) est écrasé par cette règle (spécificité 0,1,1 > 0,1,0).

**Correction** : exclure les `.testimony-card` de la règle générique blockquote dans `.page__content`, via `:not(.testimony-card)`.

### 3. Séparateur doré

Changer la couleur du `border-top` de `.testimony-card__attribution` :
- **Avant** : `var(--kiyose-color-border)`
- **Après** : `var(--kiyose-color-accent)`

### 4. Layout masonry — Hauteurs naturelles

Remplacer le CSS Grid (`display: grid; grid-template-columns`) par un layout CSS multi-column (`column-count`). Chaque card garde sa hauteur naturelle et les colonnes sont équilibrées automatiquement par le navigateur.

```
 ---  ---        (souhaité : hauteurs naturelles, colonnes équilibrées)
|   ||   |
|   | ---
|   | ---
|   ||   |
|   | ---
 ---
```

Plutôt que :
```
 ---  ---        (non souhaité : hauteur imposée par la rangée Grid)
|   ||   |
|   | ---
|   |
|   |
|   |
 ---
 ---
|   |
 ---
```

**Implémentation** :
- `.testimonials-grid` : `column-count` au lieu de `display: grid + grid-template-columns`
- `.testimonials-grid .testimony-card` : `break-inside: avoid` pour éviter qu'une card soit coupée entre deux colonnes
- Espacement vertical des cards via `margin-bottom` (pas de `gap` en multi-column)

## Fichiers impactés

| Fichier | Modification |
|---|---|
| `latelierkiyose/inc/shortcodes.php` | Défaut `columns` : 3 → 2 |
| `latelierkiyose/assets/css/components/page.css` | Exclure `.testimony-card` du sélecteur blockquote |
| `latelierkiyose/assets/css/components/testimony.css` | Séparateur accent |
| `latelierkiyose/assets/css/components/testimonials-grid.css` | CSS multi-column au lieu de CSS Grid |

## Critères d'acceptation

- [x] Grille par défaut = 2 colonnes sur viewport ≥ 768px, 1 colonne en dessous
- [x] Fond des cards = blanc (#ffffff), vérifié dans le contexte `.page__content`
- [x] Séparateur contenu/attribution = doré (`--kiyose-color-accent`)
- [x] Chaque card a sa hauteur naturelle (pas de hauteur imposée par la rangée)
- [x] Les colonnes sont équilibrées automatiquement
- [x] Pas de card coupée entre deux colonnes (`break-inside: avoid`)
- [x] Pas de régression visuelle sur les `<blockquote>` standard dans les pages de contenu
- [x] Pas de régression sur le carousel (homepage ou shortcode)
- [x] `make test` passe
