# PRD 0061 — Composant CTA doré centré

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

La page d'accueil contient deux CTA dorés « Réserver votre appel découverte » qui reprennent le même rendu : bouton jaune doré, texte bordeaux, coins arrondis, hover légèrement plus foncé, focus visible et position centrée.

Aujourd'hui, ce rendu est propre aux partials de la home. Une rédactrice ne peut pas insérer le même CTA dans une page ou un article sans recréer manuellement un bouton Gutenberg, avec le risque d'obtenir un rendu différent.

## Objectif

Créer un composant CTA réutilisable dans l'éditeur WordPress :

1. texte configurable ;
2. lien configurable ;
3. bouton toujours centré ;
4. rendu identique aux CTA dorés de la home.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Surface d'usage | Shortcode `[kiyose_cta]` | Cohérent avec les composants éditoriaux existants (`kiyose_callout`, `kiyose_signets`) |
| Alignement | Toujours centré, sans option | Le besoin demande un composant stable et non configurable sur l'alignement |
| Style | CSS partagé `.kiyose-cta-wrapper` + `.kiyose-cta` | Évite la duplication entre home et shortcode |
| CTA home | Les CTA existants adoptent la classe partagée | Le rendu de référence et le composant réutilisable restent synchronisés |
| Lien externe | Pas d'option `target="_blank"` | Non demandé ; éviter une complexité et des règles d'accessibilité supplémentaires |

## Spécifications

### 1. Shortcode

Ajouter le shortcode :

```text
[kiyose_cta texte="Réserver votre appel découverte" lien="/contact/"]
```

Paramètres :

| Paramètre | Obligatoire | Rôle |
|---|---:|---|
| `texte` | oui | Libellé visible dans le bouton |
| `lien` | oui | URL cible du bouton |

Règles :

- si `texte` est vide, ne rien rendre ;
- si `lien` est vide ou rejeté par la sanitization WordPress, ne rien rendre ;
- sanitizer `texte` avec `sanitize_text_field()` ;
- sanitizer `lien` avec `esc_url_raw()` ;
- échapper la sortie avec `esc_html()` et `esc_url()`.

### 2. Markup attendu

Le shortcode génère :

```html
<div class="kiyose-cta-wrapper">
	<a href="/contact/" class="kiyose-cta">Réserver votre appel découverte</a>
</div>
```

Le wrapper garantit le centrage. Le shortcode ne propose pas de variante gauche, droite ou pleine largeur.

### 3. Style partagé

Créer `assets/css/components/cta.css`.

Le bouton `.kiyose-cta` reprend les valeurs de référence des CTA home :

- fond et bordure `var(--kiyose-color-gold-light)` ;
- texte `var(--kiyose-color-burgundy)` ;
- `border-radius: 8px` ;
- `display: inline-flex` ;
- `font-size: var(--kiyose-font-size-lg)` ;
- `font-weight: 600` ;
- `min-height: 44px` et `min-width: 44px` ;
- padding `var(--kiyose-spacing-md) var(--kiyose-spacing-xl)` ;
- hover `#E0B45E` avec translation verticale uniquement si `prefers-reduced-motion: no-preference` ;
- focus visible `2px solid var(--kiyose-color-burgundy)` avec `outline-offset: 4px`.

### 4. Chargement conditionnel

Ajouter l'asset `kiyose-cta` au registre d'assets.

Le CSS charge si :

- la page utilise le template home ;
- ou le contenu courant contient `[kiyose_cta]`.

### 5. Documentation utilisateur

Mettre à jour :

- `doc/user/04-shortcodes.md` ;
- une recette dédiée dans `doc/user/recettes/`.

Le ton reste non technique, au tutoiement, avec un exemple copiable.

## Accessibilité

- Le bouton conserve une cible minimale de 44px.
- Le focus clavier est visible.
- Le contraste texte bordeaux sur fond doré doit rester conforme WCAG 2.2 AA.
- Le texte du bouton doit rester explicite hors contexte, par exemple « Réserver votre appel découverte ».

## Hors périmètre

- Créer un bloc Gutenberg custom.
- Ajouter des icônes.
- Ajouter des variantes visuelles.
- Ajouter une option d'alignement.
- Ajouter une option `target="_blank"`.
- Modifier l'URL constante de l'appel découverte.

## Tests et validation

### Tests automatisés

- [x] `kiyose_cta_shortcode()` rend un CTA centré avec texte et lien.
- [x] Le texte est sanitizé et échappé.
- [x] Le shortcode ne rend rien sans texte ou sans lien.
- [x] Le CSS `kiyose-cta` charge sur la home.
- [x] Le CSS `kiyose-cta` charge quand `[kiyose_cta]` est présent.
- [x] Le CSS source contient les règles de centrage, état hover et focus visible.

### Validation finale

- [x] `./bin/phpunit.sh --testdox` passe.
- [x] `make test` passe.
- [x] Vérification visuelle responsive 320px+.
