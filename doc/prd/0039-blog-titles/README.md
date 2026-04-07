# PRD 0039 — Titres des articles de blog sur la page d'accueil

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-04-07

## Contexte

Sur la page d'accueil, les titres des articles de blog (`.blog-card__title`) utilisent la police **Dancing Script** (héritée de la règle globale `h2` dans `main.css`) en taille `--kiyose-font-size-xl` (1.625rem / 26px). Cette police script calligraphique, combinée à la taille, rend les titres visuellement trop imposants par rapport au reste de la carte.

État actuel dans `blog-card.css` :

| Propriété | Valeur desktop | Valeur mobile (< 768px) |
|---|---|---|
| `font-family` | Dancing Script (héritée de `h2`) | idem |
| `font-size` | `--kiyose-font-size-xl` (1.625rem) | `--kiyose-font-size-lg` (1.25rem) |
| `line-height` | `--kiyose-line-height-snug` (**non définie**) | idem |
| `font-weight` | 700 (héritée de `h2`) | idem |

**Bug associé** : la variable `--kiyose-line-height-snug` est référencée dans `blog-card.css:101` et `blog-single.css:342` mais jamais déclarée dans `variables.css`. Le navigateur ignore la valeur et utilise le `line-height` hérité.

## Objectif

1. Passer les titres de blog cards en **Nunito** (police body) pour un rendu plus lisible et moins imposant
2. Réduire la taille à `--kiyose-font-size-lg` (1.25rem / 20px) en desktop
3. Corriger le bug de la variable `--kiyose-line-height-snug` manquante

## Spécifications CSS

### 1. Définir `--kiyose-line-height-snug` — `variables.css`

Ajouter dans la section line-height (après `--kiyose-line-height-tight: 1.2`) :

```css
--kiyose-line-height-snug: 1.3;
```

**Justification** : valeur intermédiaire entre `tight` (1.2, headings) et `normal` (1.6, body). Adaptée aux titres courts multi-lignes dans un contexte de carte.

### 2. Modifier `.blog-card__title` — `blog-card.css`

```css
.blog-card__title {
	color: var(--kiyose-color-burgundy);
	font-family: var(--kiyose-font-body);
	font-size: var(--kiyose-font-size-lg);
	font-weight: 700;
	line-height: var(--kiyose-line-height-snug);
	margin: 0;
}
```

Changements par rapport à l'existant :
- **Ajout** `font-family: var(--kiyose-font-body)` → Nunito (override l'héritage `h2`)
- **Ajout** `font-weight: 700` → explicite (ne dépend plus de l'héritage `h2`)
- **Changement** `font-size` : `xl` → `lg` (1.625rem → 1.25rem)

### 3. Ajuster le breakpoint mobile — `blog-card.css`

```css
@media (width <= 767px) {
	.blog-card__title {
		font-size: var(--kiyose-font-size-base);
	}
}
```

Changement : `lg` → `base` (1.25rem → 1rem) pour conserver un écart proportionnel entre desktop et mobile.

### 4. Minification

Régénérer `blog-card.min.css` et `variables.min.css`.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/css/variables.css` | Ajouter `--kiyose-line-height-snug: 1.3` |
| `latelierkiyose/assets/css/variables.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-card.css` | Modifier `.blog-card__title` (font-family, font-size, font-weight) + breakpoint mobile |
| `latelierkiyose/assets/css/components/blog-card.min.css` | Régénérer |

## Accessibilité

| Critère | Validation |
|---|---|
| Contraste | Inchangé — même couleur burgundy sur fond existant |
| Lisibilité | Amélioration — Nunito est plus lisible que Dancing Script à petite taille |
| Taille minimale | 1rem (16px) minimum sur mobile — conforme |
| Navigation clavier | Non impacté (aucun changement HTML) |

## Hors périmètre

- Modification des titres de blog cards sur les pages archive/search (même composant, mais à évaluer séparément si souhaité)
- Changement du `font-weight` (reste 700)
- Modification du template PHP `content-blog.php`

## Tests et validation

### Visuel
- [ ] Les titres de blog cards sur la home sont en Nunito (pas Dancing Script)
- [ ] Taille desktop : 20px (1.25rem), taille mobile : 16px (1rem)
- [ ] Les titres restent en burgundy (`--kiyose-color-burgundy`)
- [ ] Le hover/focus passe en couleur primary avec underline
- [ ] Responsive correct à 320px, 768px, 1024px, 1440px
- [ ] Le line-height est appliqué (pas de texte trop serré ou trop espacé)

### Standards
- [ ] `make test` passe (PHPCS, Stylelint, ESLint)
- [ ] Variables CSS préfixées `--kiyose-`
