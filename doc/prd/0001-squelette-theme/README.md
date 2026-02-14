# PRD 0001 — Squelette du thème

## Statut

**terminé**

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
└── .gitignore             # Exclusions Git (vendor/, node_modules/, etc.)
```

### CI/CD (racine du dépôt)

```
wp-theme-v2/
├──.github/
│   └── workflows/
│       └── build.yml          # Pipeline CI pour validation du code
└── latelierkiyose
    ├── ...
```

## Spécifications détaillées

### `style.css`

Contient uniquement les métadonnées du thème (pas de styles CSS — ceux-ci iront dans `assets/css/`).

```css
/*
Theme Name: L'Atelier Kiyose
Theme URI: https://www.latelierkiyose.fr/
Author: Alban Dericbourg
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

**Variable globale `$content_width`** :
```php
// Déclarer AVANT la fonction de setup (en dehors)
if ( ! isset( $content_width ) ) {
	$content_width = 1200;
}
```

**Fonctionnalités à déclarer dans la fonction de setup** :
- `title-tag` — Gestion automatique de la balise `<title>`
- `post-thumbnails` — Support des images à la une
- `html5` — Markup HTML5 pour search-form, comment-form, comment-list, gallery, caption
- `custom-logo` — Logo personnalisable dans le customizer
- Enregistrement du menu `primary`
- Déclaration des tailles d'images custom :
  - `kiyose-hero` : 1920x800, crop
  - `kiyose-service-card` : 600x400, crop
  - `kiyose-testimony-thumb` : 150x150, crop

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

### `.gitignore`

Exclusions Git pour fichiers générés et dépendances :

```gitignore
# Dépendances
vendor/
node_modules/

# Fichiers système
.DS_Store
Thumbs.db

# Logs
*.log

# IDE
.idea/
.vscode/
*.swp
*.swo
```

### `phpcs.xml`

Configuration PHPCS pour le projet :
- Ruleset : `WordPress`
- Chemins à scanner : `latelierkiyose/`
- Text domain : `kiyose`
- Préfixes : `kiyose_`, `Kiyose_`
- PHP minimum : `8.1`
- WP minimum : `6.7`
- Exclusions : `vendor/`, `node_modules/`

### `phpunit.xml`

**Note** : La création de ce fichier sera traitée dans un PRD ultérieur (lorsque les premiers tests seront implémentés). PHPUnit ne fonctionne pas correctement avec un fichier de configuration vide — il nécessite au minimum une configuration de test suite pointant vers des tests existants.

### `.github/workflows/build.yml`

Pipeline CI GitHub Actions pour validation automatique du code à chaque push et pull request.

**Jobs à exécuter :**

1. **PHP Code Standards (PHPCS)**
   - Environnement : PHP 8.1+
   - Actions :
     - Checkout du code
     - Installation de PHP avec extensions nécessaires
     - Installation des dépendances Composer (`composer install`)
     - Exécution de PHPCS (`./vendor/bin/phpcs`)
   - Échec si : PHPCS détecte des violations des standards WordPress

2. **PHP Unit Tests**
   - Environnement : PHP 8.1+
   - Actions :
     - Checkout du code
     - Installation de PHP avec extensions nécessaires
     - Installation des dépendances Composer
     - Exécution de PHPUnit (`./vendor/bin/phpunit`)
   - Échec si : Des tests échouent ou erreurs PHP détectées
   - Note : À ce stade (PRD 0001), aucun test n'existe encore — ce job peut être optionnel ou passer avec 0 tests

**Déclencheurs :**
- Push sur toutes les branches
- Pull requests vers `main`

**Contraintes :**
- Utiliser les actions officielles GitHub (`actions/checkout`, `shivammathur/setup-php`)
- Cache Composer pour accélérer les builds (`actions/cache`)
- Afficher clairement les erreurs PHPCS dans les logs
- Temps d'exécution cible : < 2 minutes

## Tests et validation

### Tests automatisés (local)
- [ ] `composer install` s'exécute sans erreur
- [ ] `./vendor/bin/phpcs` passe sans erreur sur tous les fichiers PHP du thème

### Tests automatisés (CI)
- [ ] Le workflow GitHub Actions `.github/workflows/build.yml` est déclenché sur push
- [ ] Le job PHPCS passe avec succès (statut vert)
- [ ] Le job PHPUnit s'exécute sans erreur (peut passer avec 0 tests à ce stade)
- [ ] Le temps d'exécution total du pipeline est < 2 minutes

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
- Pas de fichier `phpunit.xml` (sera créé lors de l'implémentation des premiers tests dans un PRD ultérieur)
