# Ajout de Prettier pour le formatage JavaScript

## Résumé

Prettier a été ajouté comme formatteur automatique de code JavaScript, complétant ESLint qui se concentre sur la qualité du code et les erreurs logiques.

## Pourquoi Prettier + ESLint ?

- **Prettier** : Formatage automatique (indentation, espaces, quotes, etc.)
- **ESLint** : Qualité du code, erreurs logiques, accessibilité

Les deux outils sont complémentaires et configurés pour être 100% compatibles via `eslint-config-prettier`.

## Configuration

### .prettierrc.json

Configuration alignée avec ESLint et WPCS :

- **useTabs**: `true` (cohérence avec WPCS)
- **singleQuote**: `true` (cohérence avec ESLint)
- **semi**: `true` (semi-colons obligatoires)
- **printWidth**: `100` (largeur de ligne raisonnable)
- **endOfLine**: `lf` (Unix line endings)
- **trailingComma**: `es5` (virgules finales ES5)

### Intégration ESLint

`eslint-config-prettier` a été ajouté à `.eslintrc.json` pour désactiver toutes les règles de formatage d'ESLint qui entrent en conflit avec Prettier.

## Fichiers créés/modifiés

### Créés
- `.prettierrc.json` — Configuration Prettier
- `.prettierignore` — Fichiers à ignorer (node_modules, vendor, *.min.js)
- `bin/prettier.sh` — Script Docker pour exécuter Prettier sans Node.js local

### Modifiés
- `package.json` :
  - Ajout de `prettier` et `eslint-config-prettier` en devDependencies
  - Nouveaux scripts npm : `format:js`, `format:check`
- `.eslintrc.json` :
  - Ajout de `"prettier"` dans `extends` pour désactiver les règles conflictuelles
- `Makefile` :
  - Nouvelles commandes : `format-js`, `format-check`
  - Mise à jour de `.PHONY`
- `doc/linting.md` :
  - Documentation Prettier ajoutée
  - Section "Workflow de développement recommandé"
- `README.md` :
  - Mention de Prettier dans les commandes et standards

## Utilisation

### Formater tous les fichiers JS

```bash
# Via Make (recommandé)
make format-js

# Via npm
npm run format:js

# Via Docker
./bin/prettier.sh --write 'latelierkiyose/assets/js/**/*.js'
```

### Vérifier le formatage sans modifier

```bash
# Via Make
make format-check

# Via npm
npm run format:check

# Via Docker
./bin/prettier.sh --check 'latelierkiyose/assets/js/**/*.js'
```

## Workflow recommandé

1. **Écrire du code** normalement dans votre éditeur
2. **Formater avec Prettier** : `make format-js` (formatage automatique)
3. **Valider avec ESLint** : `make lint-js` (détection d'erreurs logiques)
4. **Corriger les erreurs ESLint** manuellement ou avec `make lint-fix`

**Important** : Prettier doit être exécuté **avant** ESLint, car il gère uniquement le formatage, tandis qu'ESLint gère la qualité du code.

## Automatisation

Tous les fichiers JavaScript existants ont été formatés avec Prettier lors de l'installation.

Pour formater automatiquement les fichiers à chaque modification, vous pouvez :

1. **Configurer votre éditeur** pour formater à la sauvegarde (recommandé)
2. **Utiliser un hook Git pre-commit** (peut être ajouté ultérieurement avec husky + lint-staged)

## Intégration éditeur

### VS Code

Installer l'extension "Prettier - Code formatter" et ajouter à `.vscode/settings.json` :

```json
{
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "editor.formatOnSave": true,
  "[javascript]": {
    "editor.defaultFormatter": "esbenp.prettier-vscode"
  }
}
```

### Autres éditeurs

Prettier est supporté par la plupart des éditeurs modernes. Consulter https://prettier.io/docs/en/editors.html

## Bénéfices

1. **Gain de temps** : Plus besoin de se soucier du formatage manuel
2. **Cohérence** : Code uniformément formaté dans tout le projet
3. **Moins de débats** : Le formatage est automatisé, pas de discussions sur les préférences
4. **Compatibilité ESLint** : Aucun conflit entre Prettier et ESLint
5. **Alignement WPCS** : Tabulations comme dans le code PHP

## Notes

- Prettier ne détecte **pas** les erreurs de code, seulement le formatage
- ESLint reste nécessaire pour détecter les bugs et problèmes d'accessibilité
- Les deux outils sont complémentaires et doivent être utilisés ensemble
