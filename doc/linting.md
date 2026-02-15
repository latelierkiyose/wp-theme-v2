# Linting et Formatage - JavaScript et CSS

Ce document décrit les outils de linting et de formatage disponibles pour valider et formater le code JavaScript et CSS du thème.

## Outils

Le projet utilise :

- **ESLint** pour linter JavaScript (ES6+)
- **Prettier** pour formater JavaScript (formatage automatique)
- **Stylelint** pour linter CSS

## Configuration

### ESLint (.eslintrc.json)

- **Environnement** : Navigateur, ES2021
- **Modules** : ES6 modules
- **Intégration Prettier** : `eslint-config-prettier` désactive les règles conflictuelles
- **Règles principales** :
  - Indentation : tabulations
  - Quotes : simples (`'`)
  - Semi-colons : obligatoires
  - Pas de `var`, préférer `const`
  - Fonctions fléchées recommandées
  - Plugin `jsx-a11y` pour l'accessibilité

### Prettier (.prettierrc.json)

- **Indentation** : Tabulations (cohérence avec WPCS)
- **Quotes** : Simples (`'`)
- **Semi-colons** : Obligatoires
- **Largeur de ligne** : 100 caractères
- **Fin de ligne** : LF (Unix)
- **Trailing comma** : ES5

**Note importante** : Prettier est configuré pour être 100% compatible avec ESLint. Les règles de formatage d'ESLint sont désactivées via `eslint-config-prettier`.

### Stylelint (.stylelintrc.json)

- **Base** : `stylelint-config-standard`
- **Plugin** : `stylelint-order` (ordre alphabétique des propriétés)
- **Règles principales** :
  - Indentation : tabulations
  - Couleurs : format hex long (#ffffff), pas de noms de couleurs
  - Pas de `!important`
  - Pattern BEM pour les classes CSS
  - Custom properties préfixées `kiyose-`
  - Pas de préfixes vendor (autoprefixer recommandé à la place)

## Commandes disponibles

### Via Make (recommandé)

```bash
# Valider JavaScript
make lint-js

# Valider CSS
make lint-css

# Valider tout (JS + CSS)
make lint

# Corriger automatiquement les violations
make lint-fix

# Formater JavaScript avec Prettier
make format-js

# Vérifier le formatage sans modifier
make format-check
```

### Via npm

```bash
# Installer les dépendances
npm install

# Valider JavaScript
npm run lint:js

# Valider CSS
npm run lint:css

# Valider tout
npm run lint

# Corriger automatiquement
npm run lint:fix

# Formater JavaScript avec Prettier
npm run format:js

# Vérifier le formatage
npm run format:check
```

### Via scripts Docker

Si vous n'avez pas Node.js installé localement :

```bash
# ESLint
./bin/eslint.sh 'latelierkiyose/assets/js/**/*.js'

# Stylelint
./bin/stylelint.sh 'latelierkiyose/**/*.css'

# Prettier
./bin/prettier.sh --write 'latelierkiyose/assets/js/**/*.js'
```

## Intégration CI/CD

Les linters sont exécutés automatiquement dans le workflow GitHub Actions lors de chaque push ou pull request :

1. **Job `test`** : exécute PHPCS, ESLint, Stylelint et PHPUnit
2. Le build échoue si un linter détecte des erreurs

Voir `.github/workflows/build.yml` pour les détails.

## Fichiers ignorés

### .eslintignore
- `node_modules/`
- `vendor/`
- `uploads/`
- `*.min.js`
- `latelierkiyose/assets/js/vendor/`

### .stylelintignore
- `node_modules/`
- `vendor/`
- `uploads/`
- `*.min.css`

### .prettierignore
- `node_modules/`
- `vendor/`
- `uploads/`
- `*.min.js`
- `latelierkiyose/assets/js/vendor/`

## Workflow de développement recommandé

1. **Écrire du code JavaScript** normalement
2. **Formater avec Prettier** : `make format-js` (automatique, rapide)
3. **Valider avec ESLint** : `make lint-js` (détecte les erreurs logiques et d'accessibilité)
4. **Corriger les erreurs ESLint** manuellement ou avec `make lint-fix`

**Ordre important** : Prettier d'abord, ESLint ensuite. Prettier gère le formatage, ESLint gère la qualité du code.

## Bonnes pratiques

### JavaScript

```javascript
// ✅ Bon
const myFunction = () => {
	const value = 'hello';
	return value;
};

// ❌ Mauvais
var myFunction = function() {
	var value = "hello"
	return value
}
```

### CSS

```css
/* ✅ Bon */
.my-component {
	background-color: #ffffff;
	border-radius: 4px;
	color: #333333;
	padding: 16px;
}

/* ❌ Mauvais */
.myComponent {
	padding: 16px;
	color: black !important;
	background-color: white;
	border-radius: 4px;
}
```

## Désactiver temporairement un linter

### ESLint

```javascript
// eslint-disable-next-line no-console
console.log('Debug message');
```

### Stylelint

```css
/* stylelint-disable-next-line declaration-no-important */
.override {
	color: red !important;
}
```

**Note** : N'utilisez ces désactivations qu'en cas de nécessité absolue et documentez pourquoi.

## Résolution de problèmes

### Erreur "npm not found" lors de l'exécution des linters

Installez Node.js 18+ ou utilisez les scripts Docker (`./bin/eslint.sh` et `./bin/stylelint.sh`).

### Les corrections automatiques ne fonctionnent pas

Certaines règles ne peuvent pas être corrigées automatiquement. Vérifiez les messages d'erreur et corrigez manuellement.

### Conflit avec un plugin WordPress

Si un fichier vendor/plugin génère des erreurs, ajoutez-le aux fichiers ignore (`.eslintignore` ou `.stylelintignore`).
