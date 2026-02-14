# Guide de Tests Manuels

Ce document décrit comment tester manuellement le thème L'Atelier Kiyose localement.

## Prérequis

- **Docker** et **Docker Compose** installés
- **Make** (optionnel, mais recommandé pour simplifier les commandes)
- Aucune installation de PHP ou Composer n'est requise localement

## Installation initiale

### Option 1 : Utiliser Make (recommandé)

```bash
# Installer les dépendances Composer
make install

# Démarrer WordPress
make start
```

### Option 2 : Utiliser les scripts directement

```bash
# Installer les dépendances Composer via Docker
./bin/composer.sh install

# Démarrer WordPress
docker compose up -d
```

## Accès à l'environnement local

Une fois l'environnement démarré :

- **Site WordPress** : http://127.0.0.1:8000
- **Admin WordPress** : http://127.0.0.1:8000/wp-admin
- **PHPMyAdmin** : http://127.0.0.1:40001

**Credentials WordPress** (première installation) :
- Suivez l'assistant d'installation WordPress
- Créez un compte administrateur

**Credentials PHPMyAdmin** :
- Serveur : `db`
- Utilisateur : `wordpress`
- Mot de passe : `wordpress`

## Tests du thème

### 1. Activer le thème

1. Se connecter à l'admin WordPress : http://127.0.0.1:8000/wp-admin
2. Aller dans **Apparence > Thèmes**
3. Vérifier que le thème "L'Atelier Kiyose" apparaît
4. Activer le thème
5. Vérifier qu'aucune erreur PHP n'apparaît

**Résultat attendu** :
- Le thème apparaît avec les métadonnées correctes (nom, auteur, version 0.1.0)
- L'activation se fait sans erreur
- Le site reste accessible après activation

### 2. Vérifier les métadonnées du thème

Dans **Apparence > Thèmes**, vérifier que le thème affiche :
- **Nom** : L'Atelier Kiyose
- **Version** : 0.1.0
- **Auteur** : Alban Dericbourg
- **Description** : Thème sur mesure pour L'Atelier Kiyose — centre de bien-être et développement personnel

### 3. Vérifier le rendu de la page d'accueil

1. Visiter http://127.0.0.1:8000
2. Vérifier que la page s'affiche sans erreur PHP
3. Vérifier que le template `index.php` est utilisé (page minimale)

**Résultat attendu** :
- Aucune erreur 500 ou message PHP
- Le contenu s'affiche (même si le style est minimal à ce stade)

### 4. Vérifier le chargement des assets

1. Ouvrir les DevTools du navigateur (F12)
2. Aller dans l'onglet **Network**
3. Recharger la page
4. Vérifier que les fichiers suivants sont chargés :
   - `main.css` (200 OK)
   - `main.js` (200 OK)

**Résultat attendu** :
- Les deux fichiers sont trouvés (status 200)
- `main.js` a l'attribut `defer` dans le HTML

### 5. Vérifier les supports du thème

Dans **Apparence > Personnaliser**, vérifier que :
- L'option **Identité du site > Logo** est disponible (support `custom-logo`)
- On peut ajouter une image à la une sur un article (support `post-thumbnails`)

### 6. Vérifier le menu

1. Aller dans **Apparence > Menus**
2. Créer un nouveau menu
3. Vérifier que l'emplacement **Menu principal** est disponible
4. Assigner le menu à cet emplacement

**Résultat attendu** :
- L'emplacement "Menu principal" apparaît dans les options

## Validation du code (PHPCS)

### Exécuter PHPCS

```bash
# Avec Make
make phpcs

# Ou directement
./vendor/bin/phpcs
```

**Résultat attendu** :
- Aucune erreur PHPCS
- Message de succès : "Code conforme aux standards WordPress"

### Corriger automatiquement les violations mineures

```bash
# Avec Make
make phpcs-fix

# Ou directement
./vendor/bin/phpcbf
```

## Tests de régression (à chaque modification)

Après chaque modification du code, exécuter :

1. **PHPCS** : `make phpcs`
2. **Rechargement du site** : Vérifier que le site s'affiche sans erreur
3. **Vérification des logs** : `make logs` pour voir les erreurs PHP éventuelles

## Arrêt de l'environnement

```bash
# Arrêter WordPress
make stop

# Ou directement
docker compose down
```

## Nettoyage complet

Pour supprimer les dépendances et recommencer :

```bash
# Nettoyer les fichiers générés
make clean

# Réinstaller
make install
```

## Dépannage

### Le thème n'apparaît pas dans WordPress

**Cause probable** : Le dossier `latelierkiyose/` n'est pas monté correctement dans Docker.

**Solution** :
```bash
# Arrêter et redémarrer Docker
make stop
make start

# Vérifier que le volume est bien monté
docker compose config
```

### Erreurs PHP dans les logs

**Solution** :
```bash
# Voir les logs WordPress
make logs

# Ou directement
docker compose logs -f wordpress
```

### PHPCS ne trouve pas les fichiers

**Cause probable** : Les dépendances Composer ne sont pas installées.

**Solution** :
```bash
make install
```

### Permissions sur les fichiers

Si vous rencontrez des erreurs de permissions après l'exécution de Composer :

```bash
# Réattribuer les permissions à votre utilisateur
sudo chown -R $(id -u):$(id -g) vendor/
```

## Checklist PRD 0001

Utiliser cette checklist pour valider l'implémentation du PRD 0001 :

### Tests automatisés (local)
- [ ] `make install` s'exécute sans erreur
- [ ] `make phpcs` passe sans erreur sur tous les fichiers PHP du thème

### Tests automatisés (CI)
- [ ] Le workflow GitHub Actions `.github/workflows/build.yml` est déclenché sur push
- [ ] Le job PHPCS passe avec succès (statut vert)
- [ ] Le job PHPUnit s'exécute sans erreur (peut passer avec 0 tests à ce stade)
- [ ] Le temps d'exécution total du pipeline est < 2 minutes

### Tests manuels
- [ ] `make start` démarre sans erreur
- [ ] Le thème "L'Atelier Kiyose" apparaît dans Apparence > Thèmes dans l'admin WordPress
- [ ] Le thème peut être activé sans erreur
- [ ] La page d'accueil affiche le contenu du `index.php` fallback sans erreur PHP
- [ ] `style.css` affiche les métadonnées correctes (nom, version, auteur)
- [ ] Les assets CSS et JS se chargent correctement (vérifier dans DevTools)
- [ ] Le menu "Menu principal" est disponible dans Apparence > Menus
- [ ] L'option "Logo" est disponible dans Apparence > Personnaliser

## Ressources

- **Docker Compose** : https://docs.docker.com/compose/
- **WordPress Coding Standards** : https://developer.wordpress.org/coding-standards/wordpress-coding-standards/
- **PHPCS** : https://github.com/squizlabs/PHP_CodeSniffer
