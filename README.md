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

## 🛠 Commandes disponibles

```bash
make help          # Afficher l'aide
make install       # Installer les dépendances Composer
make start         # Démarrer WordPress
make stop          # Arrêter WordPress
make phpcs         # Valider le code avec PHPCS
make phpcs-fix     # Corriger automatiquement les violations
make test          # Exécuter tous les tests
make logs          # Afficher les logs WordPress
make clean         # Nettoyer les fichiers générés
```

## 🐳 Scripts Docker

Si vous n'avez pas PHP installé localement, utilisez les wrappers Docker :

```bash
./bin/composer.sh install           # Composer via Docker
./bin/phpcs.sh                      # PHPCS via Docker
./bin/phpcbf.sh                     # PHPCBF via Docker
```

## Chargement des données de production

1. Télécharger le répertoire `uploads` depuis l'environnement à recopier
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
├── composer.json            # Dépendances dev
└── phpcs.xml                # Configuration PHPCS
```

## ✅ Standards

- **WordPress Coding Standards (WPCS)** — Validation automatique via PHPCS
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

## 📝 Licence

GPL v2 or later
