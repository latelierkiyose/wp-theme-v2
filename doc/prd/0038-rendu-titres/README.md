# PRD 0038 — Scribble doré sous tous les titres h1

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-04-06

## Contexte

Le titre `h1` de la page d'accueil est souligné par un scribble doré ondulé (pseudo-élément `::after` avec SVG inline), cohérent avec l'identité Kintsugi du site. Aucun autre `h1` du thème ne bénéficie de cette décoration.

Pages concernées (10 templates) :

| Template | Classe h1 actuelle | Scribble actuel |
|---|---|---|
| `page-home.php` | `.welcome-block__title` | ✓ (welcome-block.css) |
| `page.php` | `.page__title` | ✗ |
| `page-about.php` | `.about-page__title` | ✗ |
| `page-services.php` | `.service-page__title` | ✗ |
| `page-contact.php` | `.contact-page__title` | ✗ |
| `page-calendar.php` | `.calendar-page__title` | ✗ |
| `single.php` | `.blog-single__title` | ✗ |
| `archive.php` | `.blog-archive__title` | ✗ |
| `search.php` | _(pas de classe)_ | ✗ |
| `404.php` | `.error-404__title` | ✗ |

## Objectif

Généraliser le scribble doré à **tous les `h1`** du thème via une règle CSS globale, pour renforcer l'identité visuelle Kintsugi sur l'ensemble du site.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Stratégie CSS | Règle globale `h1` / `h1::after` dans `main.css` | 1 seul endroit à maintenir ; couvre automatiquement les futurs templates |
| SVG | Même tracé ondulé inline que l'accueil | Cohérence visuelle |
| Couleur | `#E6A528` (= `--kiyose-color-gold`) | Identité Kintsugi existante |
| Largeur scribble | `width: 60%` du h1 | Proportion harmonieuse, identique à l'accueil |
| Nettoyage | Supprimer le `::after` dupliqué dans `welcome-block.css` | Éviter la double déclaration |

## Spécifications CSS

### Modifications dans `main.css`

Ajouter `display` et `position` au sélecteur `h1` existant (l. 72-74), puis ajouter le pseudo-élément `::after` :

```css
h1 {
	display: inline-block;
	font-size: var(--kiyose-font-size-5xl);
	position: relative;
}

/* Soulignement décoratif doré — esprit kintsugi, purement décoratif */
h1::after {
	background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 10'%3E%3Cpath d='M4,6 C50,3 80,8 100,5 C130,2 165,7 196,5' stroke='%23E6A528' stroke-width='4' stroke-linecap='round' fill='none'/%3E%3C/svg%3E") no-repeat center / 100% 100%;
	content: '';
	display: block;
	height: 8px;
	margin: var(--kiyose-spacing-xs) auto 0;
	pointer-events: none;
	width: 60%;
}
```

### Nettoyage dans `welcome-block.css`

Supprimer le bloc `.welcome-block__title::after` (l. 44-53) devenu redondant.

Les propriétés `display: inline-block` et `position: relative` de `.welcome-block__title` (l. 35, 41) deviennent également redondantes mais peuvent être conservées sans impact.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/css/main.css` | Ajouter `display: inline-block`, `position: relative` à `h1` + ajouter `h1::after` |
| `latelierkiyose/assets/css/main.min.css` | Regénérer (minification) |
| `latelierkiyose/assets/css/components/welcome-block.css` | Supprimer `.welcome-block__title::after` |
| `latelierkiyose/assets/css/components/welcome-block.min.css` | Regénérer (minification) |

## Accessibilité

| Critère | Validation |
|---|---|
| Rôle du scribble | Purement décoratif (`content: ''` → invisible aux lecteurs d'écran) |
| Contraste | Non applicable (décoration, pas de contenu informatif) |
| `pointer-events: none` | N'interfère pas avec les interactions clavier/souris |
| `prefers-reduced-motion` | Non applicable (élément statique, pas d'animation) |

## Hors périmètre

- Scribble sur les `h2` ou autres niveaux de heading (évaluation future si souhaité)
- Variation du tracé SVG par page (même scribble partout)
- Animation du scribble au scroll

## Tests et validation

### Fonctionnel
- [ ] Le scribble doré apparaît sous le h1 sur **toutes** les pages : accueil, à propos, services, contact, calendrier, blog single, archive, recherche, 404
- [ ] Le scribble est centré horizontalement sous le texte du titre
- [ ] Aucun double scribble sur la page d'accueil après nettoyage

### Responsive
- [ ] Rendu correct à 320px, 768px, 1024px, 1440px
- [ ] Le scribble suit la largeur du titre (60%) y compris sur titres longs qui passent sur 2 lignes

### Accessibilité
- [ ] Le scribble est invisible aux lecteurs d'écran (pas d'`aria-label`, `content: ''`)
- [ ] Navigation clavier non impactée

### Standards
- [ ] `make test` passe (PHPCS + Stylelint + ESLint)
