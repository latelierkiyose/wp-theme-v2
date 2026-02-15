# Git Hooks

## Vue d'ensemble

Le projet utilise un hook `pre-commit` pour valider automatiquement le code avant chaque commit. Ce hook vérifie la conformité du code avec les standards du projet (WPCS, ESLint, Stylelint).

## Installation

```bash
make install-hooks
```

Cette commande copie le hook `pre-commit` depuis `bin/hooks/pre-commit` vers `.git/hooks/pre-commit` et le rend exécutable.

## Fonctionnement

À chaque tentative de commit, le hook `pre-commit` :

1. **Identifie les fichiers modifiés** (`git diff --cached`) dans les zones suivantes :
   - Fichiers PHP dans `latelierkiyose/`
   - Fichiers JavaScript dans `latelierkiyose/assets/js/`
   - Fichiers CSS dans `latelierkiyose/`

2. **Exécute les validations appropriées** :
   - **PHPCS** : Vérifie les standards WordPress (indentation, nommage, sécurité)
   - **ESLint** : Vérifie les standards JavaScript et l'accessibilité
   - **Stylelint** : Vérifie les standards CSS (ordre des propriétés, formatage)

3. **Bloque le commit** si des erreurs sont détectées

4. **Affiche les erreurs** et suggère les commandes de correction

## Exemple de sortie

### Succès (commit autorisé)

```
Running pre-commit checks...

Checking PHP files with PHPCS...
✓ PHPCS check passed

Checking JavaScript files with ESLint...
✓ ESLint check passed

Checking CSS files with Stylelint...
✓ Stylelint check passed

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
All checks passed! ✓
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### Échec (commit bloqué)

```
Running pre-commit checks...

Checking PHP files with PHPCS...
✗ PHPCS check failed!
Run 'make phpcs-fix' to auto-fix issues, or 'make phpcs' to see details.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Commit aborted: Fix the errors above before committing.
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

## Corrections automatiques

### PHPCS (PHP)

```bash
make phpcs-fix
```

Corrige automatiquement :
- Indentation (tabs vs spaces)
- Espaces superflus
- Alignement des éléments
- Certains problèmes de formatage

**Note** : Certaines erreurs (nommage, sécurité) doivent être corrigées manuellement.

### ESLint (JavaScript)

```bash
make lint-fix
# ou
npm run lint:fix
```

Corrige automatiquement :
- Guillemets simples/doubles
- Points-virgules manquants
- Indentation
- Espaces autour des opérateurs

### Stylelint (CSS)

```bash
make lint-fix
# ou
npm run lint:css -- --fix
```

Corrige automatiquement :
- Ordre des propriétés
- Indentation
- Espaces et sauts de ligne
- Quotes et casse des valeurs

## Bypass du hook (non recommandé)

Si vous devez absolument contourner le hook (par exemple pour un commit de travail en cours) :

```bash
git commit --no-verify
```

**Attention** : Cette pratique est fortement déconseillée car :
- Elle introduit du code non conforme dans l'historique
- Elle peut bloquer le CI/CD
- Elle rend le code plus difficile à maintenir

**Recommandation** : Utilisez plutôt `git stash` pour sauvegarder temporairement vos modifications :

```bash
# Sauvegarder les modifications non conformes
git stash

# Faire le commit avec les fichiers conformes
git commit -m "Message"

# Restaurer les modifications
git stash pop
```

## Désinstallation

Si vous souhaitez désinstaller le hook :

```bash
rm .git/hooks/pre-commit
```

## Détails techniques

### Localisation des fichiers

- **Hook source** : `bin/hooks/pre-commit`
- **Hook installé** : `.git/hooks/pre-commit`
- **Script d'installation** : `bin/install-hooks.sh`

### Outils utilisés

Le hook détecte automatiquement l'environnement et utilise :

1. **Docker** (si `./bin/phpcs.sh` existe) :
   - `./bin/phpcs.sh` pour PHPCS
   - `npx eslint` pour ESLint (requiert Node.js local)
   - `npx stylelint` pour Stylelint (requiert Node.js local)

2. **Installation locale** (si `vendor/bin/phpcs` existe) :
   - `vendor/bin/phpcs` pour PHPCS
   - `npx eslint` pour ESLint
   - `npx stylelint` pour Stylelint

### Configuration des linters

- **PHPCS** : `phpcs.xml` (standards WordPress + prefixes `kiyose`)
- **ESLint** : `.eslintrc.json` (standards Prettier + accessibilité)
- **Stylelint** : `.stylelintrc.json` (ordre des propriétés, formatage)

## Résolution de problèmes

### Le hook ne s'exécute pas

1. Vérifier que le hook est installé :
   ```bash
   ls -la .git/hooks/pre-commit
   ```

2. Vérifier qu'il est exécutable :
   ```bash
   chmod +x .git/hooks/pre-commit
   ```

3. Réinstaller le hook :
   ```bash
   make install-hooks
   ```

### Erreurs PHPCS "not found"

1. Installer les dépendances :
   ```bash
   make install
   ```

2. Vérifier l'installation :
   ```bash
   ./bin/phpcs.sh --version
   ```

### Erreurs ESLint/Stylelint "npm not found"

1. Installer Node.js (version 18+)

2. Installer les dépendances npm :
   ```bash
   npm install
   ```

3. Vérifier l'installation :
   ```bash
   npx eslint --version
   npx stylelint --version
   ```

## Intégration CI/CD

Le hook `pre-commit` utilise les mêmes outils et configurations que le CI/CD GitHub Actions (`.github/workflows/ci.yml`). Cela garantit que :

- Les tests locaux correspondent aux tests CI
- Les commits passent le CI s'ils passent le hook local
- Les erreurs sont détectées tôt (avant le push)

## Ressources

- [Git Hooks Documentation](https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks)
- [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer)
- [ESLint](https://eslint.org/)
- [Stylelint](https://stylelint.io/)
