# L'Atelier Kiyose - Thème WordPress v2

Thème WordPress sur mesure pour [L'Atelier Kiyose](https://www.latelierkiyose.fr/), centre de bien-être et développement personnel.

## 🚀 Démarrage rapide

**Prérequis** : Docker et Docker Compose installés. Aucune installation de PHP ou Composer n'est requise.

```bash
# Installer les dépendances
make install

# Démarrer WordPress
make start
```

**Accès** :
- WordPress : http://127.0.0.1:8000
- Admin : http://127.0.0.1:8000/wp-admin
- PHPMyAdmin : http://127.0.0.1:40001

## 📖 Documentation

- **[Guide pour les agents AI](AGENTS.md)** — Instructions complètes pour les développeurs et agents
- **[Tests manuels](doc/tests-manuels.md)** — Guide de test du thème
- **[Architecture](doc/architecture.md)** — Structure technique du projet
- **[Design System](doc/design-system.md)** — Palette, typographie, composants
- **[Standards](doc/standards.md)** — WPCS, accessibilité, sécurité
- **[Linting](doc/linting.md)** — Guide des linters JavaScript et CSS
- **[Git Hooks](doc/git-hooks.md)** — Configuration et utilisation des hooks pre-commit

## 🛠 Commandes disponibles

```bash
make help          # Afficher l'aide
make install       # Installer les dépendances Composer
make install-hooks # Installer les git hooks (pre-commit)
make start         # Démarrer WordPress
make stop          # Arrêter WordPress
make phpcs         # Valider le code PHP avec PHPCS
make phpcs-fix     # Corriger automatiquement les violations PHPCS
make lint-js       # Valider le code JavaScript avec ESLint
make lint-css      # Valider le code CSS avec Stylelint
make lint          # Valider JS et CSS
make lint-fix      # Corriger automatiquement JS et CSS
make format-js     # Formater JavaScript avec Prettier
make format-check  # Vérifier le formatage JavaScript
make release       # Préparer une version et le tag Git associé
make test          # Exécuter tous les tests (PHPCS + linters)
make logs          # Afficher les logs WordPress
make clean         # Nettoyer les fichiers générés
```

## 🐳 Scripts Docker

Si vous n'avez pas PHP ou Node.js installé localement, utilisez les wrappers Docker :

```bash
# PHP & Composer
./bin/composer.sh install           # Composer via Docker
./bin/phpcs.sh                      # PHPCS via Docker
./bin/phpcbf.sh                     # PHPCBF via Docker

# JavaScript & CSS
./bin/eslint.sh 'latelierkiyose/assets/js/**/*.js'    # ESLint via Docker
./bin/stylelint.sh 'latelierkiyose/**/*.css'          # Stylelint via Docker
./bin/prettier.sh --write 'latelierkiyose/assets/js/**/*.js'  # Prettier via Docker
```

## 🪝 Git Hooks

Le projet inclut un hook `pre-commit` qui valide automatiquement le code avant chaque commit.

### Installation

```bash
make install-hooks
```

### Fonctionnement

Le hook `pre-commit` exécute automatiquement :
- **PHPCS** sur les fichiers PHP modifiés (dans `latelierkiyose/`)
- **ESLint** sur les fichiers JavaScript modifiés
- **Stylelint** sur les fichiers CSS modifiés

Si des erreurs sont détectées, le commit est annulé et vous devez corriger les erreurs.

### Corrections automatiques

```bash
make phpcs-fix    # Corriger les erreurs PHPCS
make lint-fix     # Corriger les erreurs ESLint et Stylelint
```

### Bypass du hook (non recommandé)

```bash
git commit --no-verify
```

**Note** : Le bypass est déconseillé car il peut introduire du code non conforme aux standards du projet.

## Chargement des données de production

1. Télécharger le répertoire `uploads` depuis l'environnement à recopier
```sh
kiyose_ssh_user=<user>
scp -r ${kiyose_ssh_user}@ftp.cluster029.hosting.ovh.net:/home/${kiyose_ssh_user}/www/wp-content/uploads .
```
2. Charger un dump de la base de données
3. Changer l'URL de base
```sql
UPDATE `wp_akiy_options` SET `option_value` = 'http://localhost:8000' WHERE `option_name` = 'siteurl'; 
UPDATE `wp_akiy_options` SET `option_value` = 'http://localhost:8000' WHERE `option_name` = 'home'; 
```
4. Réinitialiser le mot de passe
```sql
UPDATE `wp_akiy_users` SET `user_pass` = MD5('<new password>') WHERE `user_login` = '<user>'; 
```


## 📦 Structure du projet

```
wp-theme-v2/
├── latelierkiyose/          # Thème WordPress
│   ├── style.css            # Métadonnées du thème
│   ├── functions.php        # Point d'entrée
│   ├── index.php            # Template fallback
│   ├── inc/                 # Modules PHP
│   └── assets/              # CSS, JS, images
├── doc/                     # Documentation complète
├── bin/                     # Scripts Docker
├── Makefile                 # Commandes simplifiées
├── composer.json            # Dépendances PHP (dev)
├── package.json             # Dépendances Node.js (linters)
├── phpcs.xml                # Configuration PHPCS
├── .eslintrc.json           # Configuration ESLint
├── .prettierrc.json         # Configuration Prettier
└── .stylelintrc.json        # Configuration Stylelint
```

## ✅ Standards

- **WordPress Coding Standards (WPCS)** — Validation automatique via PHPCS
- **ESLint** — Linting JavaScript (ES6+) avec règles d'accessibilité
- **Prettier** — Formatage automatique JavaScript
- **Stylelint** — Linting CSS avec ordre des propriétés
- **WCAG 2.2 AA** — Accessibilité prioritaire
- **PHP 8.1+** — WordPress 6.7+
- **CI/CD** — GitHub Actions pour validation automatique

## 🎯 Statut du projet

**État actuel** : PRD 0001 terminé — Squelette du thème opérationnel

- ✅ Structure du thème créée
- ✅ PHPCS, Composer, CI/CD configurés
- 📋 Prochaine étape : Implémenter PRD 0002 (Design tokens)

## 🔗 Liens utiles

- Site actuel : https://www.latelierkiyose.fr/
- Ancien thème : https://github.com/latelierkiyose/wp-theme
- PRD : [doc/prd/](doc/prd/)
