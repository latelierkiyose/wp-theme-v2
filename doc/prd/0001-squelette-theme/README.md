# PRD 0001 — Squelette du thème

## Objectif

Mettre en place la structure de fichiers minimale du thème WordPress pour qu'il soit activable dans l'admin, avec les outils de qualité de code configurés.

## Dépendances

Aucune. C'est le point de départ.

## Fichiers à créer

### Thème WordPress

```
latelierkiyose/
├── style.css              # Métadonnées du thème uniquement (pas de styles)
├── functions.php          # Point d'entrée, charge les modules inc/
├── index.php              # Template fallback minimal (obligatoire WordPress)
├── inc/
│   ├── setup.php          # Configuration thème (supports, menus, image sizes)
│   └── enqueue.php        # Chargement des assets CSS/JS
└── assets/
    ├── css/
    │   └── main.css       # Fichier CSS principal (vide pour l'instant)
    └── js/
        └── main.js        # Fichier JS principal (vide pour l'instant)
```

### Outils de développement (racine du projet)

```
wp-theme-v2/
├── composer.json          # Dépendances dev (PHPCS, PHPUnit)
├── phpcs.xml              # Configuration PHPCS pour le projet
└── phpunit.xml            # Configuration PHPUnit (optionnel à ce stade)
```

## Spécifications détaillées

### `style.css`

Contient uniquement les métadonnées du thème (pas de styles CSS — ceux-ci iront dans `assets/css/`).

```css
/*
Theme Name: L'Atelier Kiyose
Theme URI: https://www.latelierkiyose.fr/
Author: Alban de Ricbourg
Description: Thème sur mesure pour L'Atelier Kiyose — centre de bien-être et développement personnel.
Version: 0.1.0
Requires at least: 6.7
Requires PHP: 8.1
Text Domain: kiyose
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
```

### `functions.php`

Charge les modules depuis `inc/`. Pas de logique directe dans ce fichier.

```php
<?php
/**
 * L'Atelier Kiyose - Functions and definitions.
 *
 * @package Kiyose
 * @since   0.1.0
 */

// Setup du thème.
require_once get_template_directory() . '/inc/setup.php';

// Chargement des assets.
require_once get_template_directory() . '/inc/enqueue.php';
```

### `inc/setup.php`

Configuration du thème via le hook `after_setup_theme`.

Fonctionnalités à déclarer :
- `title-tag` — Gestion automatique de la balise `<title>`
- `post-thumbnails` — Support des images à la une
- `html5` — Markup HTML5 pour search-form, comment-form, comment-list, gallery, caption
- `custom-logo` — Logo personnalisable dans le customizer
- Enregistrement du menu `primary`
- Déclaration des tailles d'images custom :
  - `kiyose-hero` : 1920x800, crop
  - `kiyose-service-card` : 600x400, crop
  - `kiyose-testimony-thumb` : 150x150, crop
- `content_width` global à 1200

### `inc/enqueue.php`

Enregistrement et chargement des assets via `wp_enqueue_scripts`.

- Enregistrer `kiyose-main` (CSS) pointant vers `assets/css/main.css`
- Enregistrer `kiyose-main` (JS) pointant vers `assets/js/main.js`, chargé en footer (`true`)
- Ajouter un filtre `script_loader_tag` pour `defer` sur le script principal

### `index.php`

Template fallback minimal, affichant le header, un loop basique, et le footer. Ce fichier sera remplacé par des templates spécifiques dans les PRDs suivants.

### `composer.json`

Dépendances de développement :
- `squizlabs/php_codesniffer` : `^3.0`
- `wp-coding-standards/wpcs` : `^3.0`
- `phpcompatibility/phpcompatibility-wp` : `*`
- `dealerdirect/phpcodesniffer-composer-installer` : `^1.0`

### `phpcs.xml`

Configuration PHPCS pour le projet :
- Ruleset : `WordPress`
- Chemins à scanner : `latelierkiyose/`
- Text domain : `kiyose`
- Préfixes : `kiyose_`, `Kiyose_`
- PHP minimum : `8.1`
- WP minimum : `6.7`
- Exclusions : `vendor/`, `node_modules/`

## Tests et validation

### Tests automatisés
- [ ] `composer install` s'exécute sans erreur
- [ ] `./vendor/bin/phpcs` passe sans erreur sur tous les fichiers PHP du thème

### Tests manuels
- [ ] `docker compose up` démarre sans erreur
- [ ] Le thème "L'Atelier Kiyose" apparaît dans Apparence > Thèmes dans l'admin WordPress
- [ ] Le thème peut être activé sans erreur
- [ ] La page d'accueil affiche le contenu du `index.php` fallback sans erreur PHP
- [ ] `style.css` affiche les métadonnées correctes (nom, version, auteur)

## Hors périmètre

- Pas de styles visuels (juste les fichiers vides)
- Pas de header/footer (PRD 0003)
- Pas de polices ou variables CSS (PRD 0002)
- Pas de templates spécifiques (PRD 0005+)
