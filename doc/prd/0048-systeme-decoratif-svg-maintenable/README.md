# PRD 0048 — Système décoratif SVG maintenable

- **Statut** : prêt à l'implémentation
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Les fichiers `template-parts/decorative-shapes.php` et `template-parts/decorative-scribbles.php` contiennent de longs SVG inline et définissent des fonctions globales pendant le rendu du template part. Ils font respectivement 643 et 486 lignes.

Cette organisation rend les formes difficiles à maintenir :

- les partials ne sont plus de simples fichiers de rendu ;
- les fonctions sont déclarées conditionnellement avec `function_exists()` dans un contexte de template ;
- les mêmes sources SVG sont partiellement dupliquées entre formes larges et mini formes ;
- la configuration, la sélection de variante et le rendu HTML sont entremêlés.

## Objectif

Déplacer les renderers SVG et la sélection de variantes hors des template parts, afin que les partials deviennent déclaratifs et que le système décoratif soit testable.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Nouveau module | Créer `inc/decorative-renderers.php` | Les fonctions globales vivent dans `inc/`, chargé explicitement |
| Partials | Garder `template-parts/decorative-shapes.php` et `decorative-scribbles.php` comme façades de rendu | Évite de changer les appels `get_template_part()` existants |
| Variantes | Centraliser les maps type → variantes | Rend la sélection visible et testable |
| Sortie SVG | Les fonctions retournent des chaînes HTML plutôt que d'écrire directement quand c'est possible | Facilite les tests de sortie |
| Sécurité | Whitelister types, tailles, positions et valeurs numériques | Les arguments de template part restent contrôlés |

## Spécifications

### 1. Charger le module

Modifier `latelierkiyose/functions.php` pour charger `inc/decorative-renderers.php` avant `inc/decorative-config.php`.

### 2. Créer les renderers

Créer `latelierkiyose/inc/decorative-renderers.php` avec :

- `kiyose_get_decorative_shape_variants(): array`
- `kiyose_get_decorative_scribble_variants(): array`
- `kiyose_get_wave_separator_variants(): array`
- `kiyose_render_decorative_shape_svg( array $args ): string`
- `kiyose_render_decorative_scribble_svg( array $args ): string`
- `kiyose_render_wave_separator_svg( array $args ): string`

Les fonctions doivent retourner une chaîne vide si le type demandé est inconnu.

### 3. Normaliser les arguments

Ajouter des helpers privés ou préfixés :

- `kiyose_normalize_decorative_color( string $value ): string`
- `kiyose_normalize_decorative_opacity( mixed $value ): float`
- `kiyose_normalize_decorative_size( mixed $value, string $family ): string|int`
- `kiyose_normalize_decorative_position( string $value ): string`

Contraintes :

- `opacity` bornée entre `0` et `1` ;
- tailles larges limitées à `sm`, `md`, `lg` ;
- positions limitées aux classes actuellement supportées ;
- couleurs acceptées : variables CSS `--kiyose-*` ou valeurs hex/rgb existantes.

### 4. Simplifier les template parts

`template-parts/decorative-shapes.php` doit :

- lire `$args` ;
- appeler `kiyose_render_wave_separator_svg()` si `mode=separator` ;
- sinon appeler `kiyose_render_decorative_shape_svg()` ;
- echo la sortie avec le commentaire PHPCS approprié si la fonction est considérée comme auto-échappée par construction.

`template-parts/decorative-scribbles.php` doit :

- lire `$args` ;
- appeler `kiyose_render_decorative_scribble_svg()` ;
- echo la sortie.

### 5. Éviter les changements visuels

Les attributs suivants doivent rester équivalents :

- classes `.deco-shape`, `.deco-element`, modifiers position/taille ;
- `aria-hidden="true"` ;
- `role="presentation"` ;
- `focusable="false"` si présent ;
- `width`, `height`, `viewBox`, `preserveAspectRatio` ;
- variables inline `--kiyose-deco-rotation` et `--kiyose-deco-top`.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/functions.php` | Charger `inc/decorative-renderers.php` |
| `latelierkiyose/inc/decorative-renderers.php` | Nouveau module SVG |
| `latelierkiyose/template-parts/decorative-shapes.php` | Simplifier le rendu |
| `latelierkiyose/template-parts/decorative-scribbles.php` | Simplifier le rendu |
| `latelierkiyose/tests/Test_Decorative_Renderers.php` | Nouveaux tests |
| `phpcs.xml` | Ajouter les fonctions renderers à `customAutoEscapedFunctions` si nécessaire |

## Accessibilité et performance

- Les éléments décoratifs restent masqués des technologies d'assistance.
- Aucun élément décoratif ne doit devenir focusable.
- Le refactor ne doit pas augmenter le volume HTML généré.
- Le choix aléatoire de variantes peut rester non déterministe côté rendu public.

## Hors périmètre

- Changer le design des formes.
- Remplacer les SVG inline par des fichiers externes si cela empêche la coloration par variable CSS.
- Revoir les positions décoratives de la home : couvert par le PRD 0044.

## Tests et validation

### Tests automatisés

- [ ] Type inconnu → chaîne vide.
- [ ] Opacité négative → `0`, opacité supérieure à `1` → `1`.
- [ ] Taille inconnue large → fallback `md`.
- [ ] Position inconnue → fallback sûr.
- [ ] SVG rendu contient `aria-hidden="true"` et `role="presentation"`.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `make phpcs` passe.

### Validation manuelle

- [ ] Les décorations de la home restent visibles.
- [ ] Les décorations des pages internes restent visibles.
- [ ] Les séparateurs wave entre sections sont inchangés.
- [ ] Aucun warning PHP lié à des fonctions déjà déclarées.
