# PRD 0045 — Registre d'assets conditionnels

- **Statut** : prêt à l'implémentation
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

`latelierkiyose/inc/enqueue.php` fait 555 lignes et charge une grande partie des feuilles de style de composants globalement. Certaines ressources sont conditionnelles, mais la logique est dispersée dans plusieurs fonctions et dupliquée avec des appels `wp_enqueue_style()` très similaires.

Deux problèmes concrets ont été observés :

- les styles de pages spécifiques (`search`, `404`, `blog-*`, `home-*`) sont majoritairement chargés même quand ils ne sont pas utiles ;
- `kiyose_preload_fonts()` précharge `caveat-v21-latin-regular.woff2`, fichier absent du thème, alors que les polices réellement présentes sont Dancing Script et Nunito.

## Objectif

Remplacer la logique procédurale longue par un registre d'assets déclaratif, avec conditions de chargement explicites, versions cohérentes, dépendances testables et correction du preload de police.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Registre | Créer `kiyose_get_theme_assets(): array` | Une source unique décrit handles, fichiers, dépendances et conditions |
| Conditions | Utiliser des callbacks nommés ou closures simples | Les conditions deviennent lisibles et testables |
| Global vs conditionnel | Ne charger globalement que fonts, variables, header, navigation, footer, plugins-common, main, Gutenberg, kintsugi et animations | Le reste dépend du contexte de rendu |
| Versions | Utiliser `kiyose_get_asset_version()` partout | Évite les `filemtime()` directs qui cassent si un fichier source manque |
| Police critique | Précharger `dancing-script-v29-latin-regular.woff2` et `nunito-v32-latin-regular.woff2` | Correspond aux assets présents et au design system actuel |

## Spécifications

### 1. Créer un registre déclaratif

Dans `latelierkiyose/inc/enqueue.php`, ajouter :

- `kiyose_get_theme_assets(): array`
- `kiyose_enqueue_registered_asset( array $asset, string $suffix ): void`
- `kiyose_asset_condition_matches( array $asset ): bool`

Chaque asset doit être décrit avec :

| Clé | Description |
|---|---|
| `type` | `style` ou `script` |
| `handle` | Handle WordPress existant |
| `path` | Chemin non minifié relatif au thème, sans suffixe dynamique |
| `deps` | Dépendances WordPress |
| `in_footer` | Scripts uniquement |
| `condition` | Callback optionnel ; absence = global |
| `priority` | Optionnel, pour préserver les chargements tardifs comme Events Manager |

### 2. Conditions de chargement attendues

| Asset | Condition |
|---|---|
| `kiyose-page` | Pages standards et templates de page qui utilisent `.page` ou styles page génériques |
| `kiyose-search` | `is_search()` |
| `kiyose-404` | `is_404()` |
| `kiyose-blog-card` | archives, recherche, home blog ou shortcode/rendu qui affiche des cards |
| `kiyose-blog-archive` | `is_archive()` ou page des articles |
| `kiyose-blog-single` | `is_singular( 'post' )` |
| `kiyose-hero`, `kiyose-home-sections` | `templates/page-home.php` |
| `kiyose-service-page` | `templates/page-services.php` |
| `kiyose-about-page` | `templates/page-about.php` |
| `kiyose-contact-page`, `kiyose-cf7-override` | `templates/page-contact.php` |
| `kiyose-events-manager` | page calendrier, home, ou contenu avec shortcode Events Manager |
| `kiyose-testimonials-grid`, `kiyose-masonry` | contenu avec `[kiyose_testimonials]` |
| `kiyose-carousel` | home ou `[kiyose_testimonials display="carousel"]` |
| `kiyose-signets` | contenu avec `[kiyose_signets]` |
| `kiyose-callout-box` | contenu avec `[kiyose_callout]` |
| overlays et animations home | `templates/page-home.php` |
| decorative reveal/overlap | global tant que les formes décoratives peuvent apparaître sur toutes les pages |

### 3. Corriger les versions et le preload

- Remplacer les `filemtime( get_template_directory() . '/assets/...' )` directs par `kiyose_get_asset_version()`.
- Remplacer le preload Caveat absent par Dancing Script regular.
- Garder Nunito regular en preload.

### 4. Préserver la compatibilité production

`kiyose_get_asset_suffix()` reste la source de vérité :

- `.min` quand `WP_DEBUG` est absent ou `false` ;
- chaîne vide quand `WP_DEBUG` vaut `true`.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/inc/enqueue.php` | Refactor registre + conditions + preload |
| `latelierkiyose/tests/Test_Enqueue.php` | Nouveaux tests des helpers de registre |
| `doc/release/README.md` | À modifier seulement si le comportement de l'artefact livré change |

## Performance

- Réduire le nombre de fichiers CSS chargés par page.
- Conserver les dépendances nécessaires pour éviter les flashs de styles non résolus.
- Ne pas retarder les styles réellement critiques du header, de la navigation et du footer.

## Hors périmètre

- Changer la stratégie de minification.
- Bundler CSS/JS avec Vite, webpack ou équivalent.
- Supprimer les fichiers minifiés trackés : couvert par le PRD 0050 si décidé plus tard.

## Tests et validation

### Tests automatisés

- [ ] Tester `kiyose_get_asset_suffix()` pour `WP_DEBUG=true` et production.
- [ ] Tester que le registre contient les handles globaux obligatoires.
- [ ] Tester que les assets conditionnels exposent une condition.
- [ ] Tester que `kiyose_get_asset_version()` retourne `KIYOSE_VERSION` si le fichier est absent.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `make test` passe.

### Validation manuelle

- [ ] Home : styles home, carousel, overlays, Events Manager, Brevo et décorations présents.
- [ ] Page contact : styles contact et CF7 présents, styles home absents.
- [ ] Page 404 : styles 404 présents, styles contact/home absents.
- [ ] Article blog : styles single et script copy-link fonctionnels.
- [ ] Aucun preload ne pointe vers un fichier absent.
