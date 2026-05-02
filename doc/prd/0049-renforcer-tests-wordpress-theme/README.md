# PRD 0049 — Renforcement des tests du thème WordPress

- **Statut** : prêt à l'implémentation
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Le thème possède une base PHPUnit, mais elle couvre surtout des helpers purs. `Test_Shortcodes.php` vérifie principalement que le fichier existe et que des noms de fonctions sont présents. Le bootstrap mocke beaucoup de fonctions WordPress avec des no-op, ce qui limite la détection de régressions réelles sur les shortcodes, les enqueues, les composants ou les meta boxes.

Les prochains refactors structurels (PRDs 0044 à 0048) augmentent le besoin de tests ciblés pour sécuriser le comportement public.

## Objectif

Renforcer la couverture automatisée sans introduire une suite WordPress complète lourde. Les tests doivent vérifier les helpers, le HTML généré et les décisions de chargement d'assets à travers des mocks contrôlés.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Infrastructure | Garder PHPUnit avec bootstrap de mocks | Cohérent avec le projet actuel et rapide en Docker |
| Scope | Tester les seams introduits par les PRDs 0044-0048 | Les tests suivent les unités refactorisées |
| WordPress complet | Ne pas intégrer `wordpress-develop` pour cette itération | Trop coûteux pour un thème simple |
| Mocks | Remplacer progressivement les no-op par des mocks observables | Permet d'assert les appels `wp_enqueue_*`, `add_shortcode`, etc. |
| Nommage | Respecter le pattern projet `<method>_(when<condition>|of<input>)_<expected>` | Conforme aux instructions AGENTS.md |

## Spécifications

### 1. Améliorer le bootstrap de tests

Modifier `latelierkiyose/tests/bootstrap.php` pour ajouter des stores globaux de test :

- `$GLOBALS['kiyose_test_enqueued_styles']`
- `$GLOBALS['kiyose_test_enqueued_scripts']`
- `$GLOBALS['kiyose_test_shortcodes']`
- `$GLOBALS['kiyose_test_post_meta']`
- `$GLOBALS['kiyose_test_theme_mods']`

Adapter les mocks suivants pour écrire dans ces stores :

- `wp_enqueue_style()`
- `wp_enqueue_script()`
- `add_shortcode()`
- `get_post_meta()`
- `get_theme_mod()`
- `shortcode_exists()`
- `do_shortcode()`

Ajouter `kiyose_reset_test_state(): void` pour réinitialiser ces stores dans les `setUp()`.

### 2. Remplacer les tests de présence de fichiers

Réécrire `latelierkiyose/tests/Test_Shortcodes.php`.

Cas minimum :

- `kiyose_callout_shortcode_whenContentIsEmpty_returnsEmptyString`
- `kiyose_callout_shortcode_ofTitleAndContent_returnsAccessibleNote`
- `kiyose_signets_shortcode_ofManualLinks_returnsNavigation`
- `kiyose_signets_shortcode_whenNoLinksFound_returnsEmptyString`
- `kiyose_testimonials_shortcode_whenDisplayIsInvalid_fallsBackToGrid`

### 3. Ajouter tests des assets

Créer `latelierkiyose/tests/Test_Enqueue.php` après implémentation du PRD 0045.

Cas minimum :

- `kiyose_get_asset_suffix_whenWpDebugIsTrue_returnsEmptyString`
- `kiyose_get_theme_assets_returnsExpectedGlobalHandles`
- `kiyose_get_theme_assets_ofConditionalAssets_declaresConditions`
- `kiyose_get_asset_version_whenFileIsMissing_returnsThemeVersion`

### 4. Ajouter tests des composants

Créer `latelierkiyose/tests/Test_Components.php` après implémentation du PRD 0046.

Cas minimum :

- `kiyose_get_social_profiles_whenThemeModsAreEmpty_returnsDefaults`
- `kiyose_get_social_links_html_ofContactContext_returnsSocialLinksList`
- `kiyose_get_post_share_links_html_ofPostId_returnsShareButtons`
- `kiyose_get_testimonials_carousel_html_ofQuery_returnsAccessibleCarousel`

### 5. Ajouter tests home et décorations

Créer :

- `latelierkiyose/tests/Test_Home.php` après PRD 0044 ;
- `latelierkiyose/tests/Test_Decorative_Renderers.php` après PRD 0048.

Cas minimum :

- JSON meta invalide → tableau vide ;
- valeurs par défaut home présentes ;
- renderer décoratif type inconnu → chaîne vide ;
- renderer décoratif normalise opacité et taille.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/tests/bootstrap.php` | Mocks observables + reset state |
| `latelierkiyose/tests/Test_Shortcodes.php` | Remplacer les tests faibles par des tests comportementaux |
| `latelierkiyose/tests/Test_Enqueue.php` | Nouveau, dépend du PRD 0045 |
| `latelierkiyose/tests/Test_Components.php` | Nouveau, dépend du PRD 0046 |
| `latelierkiyose/tests/Test_Home.php` | Nouveau, dépend du PRD 0044 |
| `latelierkiyose/tests/Test_Decorative_Renderers.php` | Nouveau, dépend du PRD 0048 |
| `phpunit.xml` | Inchangé sauf si un nouveau dossier de tests est créé |

## Hors périmètre

- Installer la suite de tests officielle WordPress.
- Ajouter des tests E2E navigateur.
- Tester le rendu visuel pixel-perfect.
- Refactoriser le code de production sans PRD associé.

## Tests et validation

### Tests automatisés

- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `make phpcs` passe.
- [ ] Aucun test ne dépend de l'ordre d'exécution.
- [ ] Chaque classe de test réinitialise l'état global dans `setUp()`.
- [ ] Les tests ne font aucun accès réseau.

### Critères d'acceptation

- [ ] `Test_Shortcodes.php` ne contient plus de test limité à `assertFileExists()`.
- [ ] Les tests détectent une suppression accidentelle d'un handle d'asset global.
- [ ] Les tests détectent une régression d'échappement sur au moins un helper de rendu HTML.
- [ ] Les tests détectent un JSON meta invalide mal géré.
