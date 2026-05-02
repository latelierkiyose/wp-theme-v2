# PRD 0044 — Découpage du template de page d'accueil

- **Statut** : prêt à l'implémentation
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Le template `latelierkiyose/templates/page-home.php` fait aujourd'hui 863 lignes. Il mélange plusieurs responsabilités :

- lecture et valeurs par défaut des champs meta de la home ;
- rendu des sections éditoriales ;
- rendu des overlays desktop ;
- intégration Events Manager et Brevo ;
- requêtes `WP_Query` pour les témoignages et les articles ;
- configuration et rendu des formes décoratives.

Cette concentration rend les évolutions risquées : une modification sur une section oblige à charger mentalement toute la page, les duplications sont difficiles à repérer, et les tests unitaires ciblés sont quasi impossibles.

## Objectif

Réduire `page-home.php` à un orchestrateur lisible, en extrayant les données, les sections et les décorations dans des unités réutilisables et testables, sans changer le rendu HTML public ni les classes CSS existantes.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Point d'entrée | `templates/page-home.php` conserve `get_header()`, `<main>`, l'ordre des sections, puis `get_footer()` | Respecte la hiérarchie WordPress et limite le risque de régression |
| Données de page | Créer `inc/home.php` avec `kiyose_get_home_page_data( int $post_id ): array` | Centralise les `get_post_meta()`, JSON decode et valeurs par défaut |
| Sections | Créer des partials dans `template-parts/home/` | Chaque section devient modifiable indépendamment |
| Décorations de home | Créer `kiyose_get_home_page_shapes(): array` dans `inc/home.php` | Les positions décoratives quittent le template principal, comme les pages non-home utilisent déjà `inc/decorative-config.php` |
| Compatibilité | Ne pas renommer les classes CSS, IDs, shortcodes ou meta keys existants | Le refactor doit être structurel, pas fonctionnel |

## Spécifications

### 1. Charger le nouveau module

Modifier `latelierkiyose/functions.php` pour charger `inc/home.php` après `inc/content-filters.php` et avant `inc/decorative-config.php`.

### 2. Extraire la préparation des données

Créer `latelierkiyose/inc/home.php` avec :

- `kiyose_get_home_page_data( int $post_id ): array`
- `kiyose_decode_json_meta_array( string $raw_value ): array`
- `kiyose_get_home_page_shapes(): array`

`kiyose_get_home_page_data()` doit retourner les clés suivantes :

| Clé | Source actuelle |
|---|---|
| `welcome_title` | `kiyose_welcome_title` avec valeur par défaut actuelle |
| `welcome_subtitle` | `kiyose_welcome_subtitle` |
| `welcome_text` | `kiyose_welcome_text` |
| `welcome_keywords` | JSON `kiyose_welcome_keywords`, tableau vide si invalide |
| `welcome_slogan` | `kiyose_welcome_slogan` |
| `about_content` | `kiyose_hero_content` avec valeur par défaut actuelle |
| `about_cta_text` | `kiyose_hero_cta_text` avec valeur par défaut actuelle |
| `about_cta_url` | `kiyose_hero_cta_url` avec valeur par défaut actuelle |
| `about_image_id` | `kiyose_hero_image_id` |
| `content1_qa` | JSON `kiyose_content1_qa`, tableau vide si invalide |
| `content1_slogan` | `kiyose_content1_slogan` |
| `content2_text` | `kiyose_content2_text` |
| `content2_slogan` | `kiyose_content2_slogan` |

### 3. Extraire les sections en template parts

Créer les fichiers suivants :

| Fichier | Responsabilité |
|---|---|
| `template-parts/home/welcome.php` | Bloc bienvenue, mots-clefs et CTA appel découverte |
| `template-parts/home/about-mobile.php` | Section À propos mobile |
| `template-parts/home/content-qa.php` | Section Q&R `home-content1` |
| `template-parts/home/content-free.php` | Section texte libre `home-content2` |
| `template-parts/home/events.php` | Section prochaines dates avec shortcode Events Manager |
| `template-parts/home/testimonials.php` | Requête et rendu des témoignages home |
| `template-parts/home/blog.php` | Requête et rendu des 3 derniers articles |
| `template-parts/home/overlays.php` | Overlays À propos et newsletter + annonce screen reader |
| `template-parts/home/decorative-shapes.php` | Rendu des formes décoratives de page home |

Chaque partial reçoit ses données via le paramètre `$args` de `get_template_part()`.

### 4. Réduire `page-home.php`

`page-home.php` doit :

1. appeler `kiyose_get_home_page_data( get_the_ID() )` ;
2. rendre les sections dans le même ordre qu'aujourd'hui ;
3. conserver les séparateurs entre sections ;
4. appeler les nouveaux partials avec `$args` ;
5. ne plus contenir directement de `WP_Query`, `do_shortcode()`, `shortcode_exists()` ou longue liste de formes décoratives.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/functions.php` | Charger `inc/home.php` |
| `latelierkiyose/inc/home.php` | Nouveau module de données et configuration home |
| `latelierkiyose/templates/page-home.php` | Réduire au rôle d'orchestrateur |
| `latelierkiyose/template-parts/home/*.php` | Nouveaux partials de sections |
| `latelierkiyose/tests/Test_Home.php` | Nouveaux tests des helpers de données |

## Accessibilité

- Les attributs `aria-label`, `aria-labelledby`, `aria-live`, `role` et `hidden` existants doivent être conservés.
- Les éléments décoratifs restent `aria-hidden="true"` et non focusables.
- Aucun changement de navigation clavier ne doit être introduit.

## Hors périmètre

- Modifier le design ou les classes CSS de la home.
- Changer les shortcodes Events Manager ou Brevo.
- Refactoriser le carousel partagé : couvert par le PRD 0046.
- Refactoriser le système SVG décoratif global : couvert par le PRD 0048.

## Tests et validation

### Tests automatisés

- [ ] Ajouter des tests PHPUnit pour `kiyose_decode_json_meta_array()` : JSON valide, JSON invalide, chaîne vide.
- [ ] Ajouter des tests PHPUnit pour les valeurs par défaut de `kiyose_get_home_page_data()` avec mocks `get_post_meta()` adaptés.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `make test` passe après régénération éventuelle des assets.

### Validation manuelle

- [ ] La home garde le même ordre de sections.
- [ ] Les CTAs, overlays, événements, témoignages et articles s'affichent comme avant.
- [ ] Aucun warning PHP en mode `WP_DEBUG`.
- [ ] Responsive 320px, 768px, 1024px et 1440px inchangé visuellement.
