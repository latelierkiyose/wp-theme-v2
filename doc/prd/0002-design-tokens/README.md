# PRD 0002 — Design tokens et polices

## Statut

**brouillon**

## Objectif

Mettre en place le design system CSS (variables, typographie, espacements) et les polices auto-hébergées. Après cette étape, tous les tokens de design sont disponibles pour les templates suivants.

## Dépendances

- **PRD 0001** : Structure de fichiers et `inc/enqueue.php` en place.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── assets/
│   ├── css/
│   │   ├── variables.css      # Variables CSS (couleurs, espacement, typographie)
│   │   └── fonts.css          # Déclarations @font-face
│   └── fonts/
│       ├── lora-v35-latin-regular.woff2
│       ├── lora-v35-latin-regular.woff
│       ├── lora-v35-latin-700.woff2
│       ├── lora-v35-latin-700.woff
│       ├── lora-v35-latin-italic.woff2
│       ├── lora-v35-latin-italic.woff
│       ├── nunito-v26-latin-regular.woff2
│       ├── nunito-v26-latin-regular.woff
│       ├── nunito-v26-latin-600.woff2
│       ├── nunito-v26-latin-600.woff
│       ├── nunito-v26-latin-700.woff2
│       └── nunito-v26-latin-700.woff
```

### Fichiers modifiés

- `inc/enqueue.php` — Ajouter le chargement de `fonts.css` et `variables.css`
- `assets/css/main.css` — Importer les variables et ajouter les styles de base (reset minimal, body, typographie)

## Spécifications détaillées

### Téléchargement des polices

Source recommandée : [google-webfonts-helper](https://gwfh.mranftl.com/fonts) — permet de télécharger les polices Google Fonts en woff/woff2 avec subset latin uniquement.

Polices et graisses :
- **Lora** : Regular (400), Bold (700), Regular Italic (400i)
- **Nunito** : Regular (400), SemiBold (600), Bold (700)

Format : woff2 (principal) + woff (fallback)

### `assets/css/fonts.css`

Déclarations `@font-face` pour chaque police/graisse. Utiliser `font-display: swap` pour éviter le FOIT (Flash of Invisible Text).

### `assets/css/variables.css`

Reprendre les variables de `references/css-examples.css` (section CSS VARIABLES) :

**Couleurs principales** :
- `--color-primary: #D7A4A4` (Rose poudré)
- `--color-secondary: #C9ABA7` (Taupe rosé)
- `--color-accent: #E6A528` (Or miel)
- `--color-background: #EFE5E4` (Beige clair)
- `--color-burgundy: #5D0505` (Bordeaux foncé)

**Couleurs fonctionnelles** :
- `--color-text: #333333`
- `--color-text-light: #666666`
- `--color-border: #C9ABA7`
- `--color-gold: #E6A528`
- `--color-gold-light: #F4C975`
- `--color-gold-transparent: rgba(230, 165, 40, 0.3)`

**Variantes de contraste** (ajustements WCAG AA) :
- `--color-primary-dark` : Version assombrie de `#D7A4A4` pour texte sur fond clair (à calculer pour ratio ≥ 4.5:1 sur `#EFE5E4`)
- `--color-burgundy-on-primary` : Vérifier que `#5D0505` sur `#D7A4A4` atteint 4.5:1, sinon ajuster

**Typographie** :
- Variables `--font-heading`, `--font-body`
- Échelle typographique de `--font-size-xs` (0.75rem) à `--font-size-4xl` (2.5rem)

**Espacement** :
- De `--spacing-xs` (0.25rem) à `--spacing-3xl` (4rem)

**Breakpoints** (comme commentaires — CSS ne supporte pas les variables dans media queries) :
- Mobile : 320px - 767px
- Tablet : 768px
- Desktop : 1024px

### `assets/css/main.css`

Styles de base à ajouter :
- Import de `variables.css` et `fonts.css` via `@import` **ou** chargement séparé via `wp_enqueue_style` (privilégier le chargement WordPress pour le cache)
- Reset minimal (box-sizing border-box, margin 0 sur body)
- Styles body : `font-family: var(--font-body)`, `font-size: var(--font-size-base)`, `line-height: 1.6`, `color: var(--color-text)`, `background-color: var(--color-background)`
- Styles headings h1-h6 : `font-family: var(--font-heading)`, `color: var(--color-burgundy)`, `line-height: 1.3`
- Styles liens : couleur, hover, focus-visible
- Classe utilitaire `.sr-only` (screen reader only)
- Focus visible (`:focus-visible` avec outline)
- `prefers-reduced-motion` : désactiver les transitions globalement

### `inc/enqueue.php` (modification)

Ajouter le chargement de `fonts.css` et `variables.css` comme dépendances de `main.css` :
- `kiyose-fonts` → `assets/css/fonts.css`
- `kiyose-variables` → `assets/css/variables.css`
- `kiyose-main` dépend de `kiyose-fonts` et `kiyose-variables`

Ajouter le preload des polices critiques (Lora Regular, Nunito Regular) via le hook `wp_head` :
```html
<link rel="preload" href=".../lora-regular.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href=".../nunito-regular.woff2" as="font" type="font/woff2" crossorigin>
```

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur `inc/enqueue.php` modifié

### Tests manuels
- [ ] Les polices Lora et Nunito s'affichent correctement dans le navigateur
- [ ] Les variables CSS sont accessibles (vérifier dans l'inspecteur via `:root`)
- [ ] Les titres utilisent Lora, le corps utilise Nunito
- [ ] Pas de requête externe vers fonts.googleapis.com (vérifier Network tab)
- [ ] Le `font-display: swap` fonctionne (le texte est visible immédiatement, même si la police n'est pas encore chargée)
- [ ] Les couleurs de base s'appliquent (fond beige, texte foncé)

### Validation contraste
- [ ] Tester `--color-text` (#333333) sur `--color-background` (#EFE5E4) → ratio ≥ 4.5:1
- [ ] Tester `--color-burgundy` (#5D0505) sur `--color-background` (#EFE5E4) → ratio ≥ 4.5:1
- [ ] Tester `--color-text-light` (#666666) sur `--color-background` (#EFE5E4) → ratio ≥ 4.5:1
- [ ] Documenter les ajustements nécessaires dans les variables `--color-*-dark`

## Hors périmètre

- Pas de composants UI (boutons, cartes) — ceux-ci seront ajoutés au fil des templates
- Pas de styles de header/footer (PRD 0003)
- Pas d'éléments Kintsugi décoratifs (PRD 0014)
