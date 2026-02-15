# Ajout des linters JS et CSS

## Résumé

Des linters ont été ajoutés pour valider le code JavaScript et CSS du thème WordPress, complétant ainsi les outils de qualité de code existants (PHPCS pour PHP).

## Outils ajoutés

### ESLint (JavaScript)
- **Configuration**: `.eslintrc.json`
- **Ignore**: `.eslintignore`
- **Cible**: `latelierkiyose/assets/js/**/*.js`
- **Règles principales**:
  - ES6+ moderne (pas de `var`, préférer `const`)
  - Indentation par tabulations (cohérence avec WPCS)
  - Accessibilité via plugin `jsx-a11y`
  - Bonnes pratiques JavaScript modernes

### Stylelint (CSS)
- **Configuration**: `.stylelintrc.json`
- **Ignore**: `.stylelintignore`
- **Cible**: `latelierkiyose/**/*.css`
- **Règles principales**:
  - Indentation par tabulations
  - Ordre alphabétique des propriétés CSS
  - Pattern BEM pour les classes
  - Custom properties préfixées `kiyose-`
  - Pas de `!important`, pas de noms de couleurs

## Fichiers créés

```
/
├── package.json              # Dépendances npm (ESLint, Stylelint)
├── .eslintrc.json           # Configuration ESLint
├── .eslintignore            # Fichiers ignorés par ESLint
├── .stylelintrc.json        # Configuration Stylelint
├── .stylelintignore         # Fichiers ignorés par Stylelint
├── bin/
│   ├── eslint.sh            # Script Docker pour ESLint
│   └── stylelint.sh         # Script Docker pour Stylelint
└── doc/
    └── linting.md           # Documentation complète des linters
```

## Fichiers modifiés

### Makefile
Nouvelles commandes ajoutées:
- `make lint-js` — Valider JavaScript
- `make lint-css` — Valider CSS
- `make lint` — Valider JS + CSS
- `make lint-fix` — Corriger automatiquement
- `make test` — Modifié pour inclure les linters (PHPCS + ESLint + Stylelint)
- `make clean` — Modifié pour nettoyer `node_modules/`

### .github/workflows/build.yml
- Ajout de Node.js setup (v18)
- Ajout de npm cache
- Ajout de `npm ci` pour installer les dépendances
- Ajout des étapes `npm run lint:js` et `npm run lint:css`
- Renommage du job: `Tests (PHPCS + PHPUnit + Linters)`

### .gitignore
- Ajout de `package-lock.json`

### Documentation
- **README.md**: Mise à jour des commandes et de la structure
- **doc/quick-reference.md**: Ajout des commandes de linting
- **doc/linting.md**: Nouveau guide complet des linters

## Utilisation

### Développement local

```bash
# Validation
make lint           # Valider tout (JS + CSS)
make lint-js        # Valider JS uniquement
make lint-css       # Valider CSS uniquement

# Correction automatique
make lint-fix       # Corriger JS et CSS

# Tests complets (PHP + JS + CSS)
make test
```

### Sans Node.js (via Docker)

```bash
# ESLint
./bin/eslint.sh 'latelierkiyose/assets/js/**/*.js'

# Stylelint
./bin/stylelint.sh 'latelierkiyose/**/*.css'
```

### CI/CD

Les linters sont automatiquement exécutés dans GitHub Actions:
1. Sur chaque push vers `main` ou `develop`
2. Sur chaque pull request vers `main`
3. Le build échoue si des erreurs sont détectées

## Bénéfices

1. **Qualité de code**: Détection précoce des erreurs et mauvaises pratiques
2. **Cohérence**: Style de code uniforme dans tout le projet
3. **Accessibilité**: Détection de problèmes d'accessibilité dans le JS
4. **Maintenabilité**: Code plus facile à lire et à maintenir
5. **CI/CD**: Validation automatique avant déploiement

## Notes

- Les linters respectent les mêmes conventions que PHPCS (tabulations, préfixes `kiyose_`)
- Les règles peuvent être affinées selon les besoins du projet
- Les corrections automatiques (`--fix`) ne corrigent que les violations simples
- Certaines règles nécessitent des corrections manuelles
- Consulter `doc/linting.md` pour la documentation complète
