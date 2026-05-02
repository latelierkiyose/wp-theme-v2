# PRD 0050 — Validation read-only du build des assets

- **Statut** : prêt à l'implémentation
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

`make test` dépend de `lint`, et `lint` dépend de `build`. Le build exécute `npm run build:css` et `npm run build:js`, qui réécrivent les fichiers `.min.css`, `.min.js` et `.map` directement dans `latelierkiyose/assets/`.

Comme les fichiers minifiés sont trackés, une commande de validation peut modifier le working tree. Ce comportement est dangereux pour un audit, une CI ou une vérification locale : un test ne devrait pas produire de diff source sans action explicite de build.

## Objectif

Séparer clairement :

- les commandes qui génèrent des assets ;
- les commandes qui vérifient que les assets générés sont à jour sans écrire dans le dépôt.

`make test` doit devenir read-only vis-à-vis des fichiers trackés.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Build existant | Garder `npm run build` comme commande mutante explicite | Utile avant livraison ou PR |
| Vérification | Ajouter `npm run build:check` | Permet CI/read-only |
| Scripts | Ajouter un mode `--check` à `bin/minify-css.js` et `bin/minify-js.js` | Réutilise la logique de minification existante |
| Makefile | Retirer `build` de la dépendance `lint` | Le lint ne doit pas générer |
| Test global | Faire dépendre `make test` de `build-check` en plus des linters/tests | Détecte les minifiés obsolètes sans les réécrire |

## Spécifications

### 1. Ajouter un mode check aux scripts de minification

Modifier `bin/minify-css.js` :

- détecter `process.argv.includes('--check')` ;
- minifier chaque fichier source en mémoire ;
- comparer le résultat attendu avec le contenu du `.min.css` existant ;
- comparer la source map attendue avec le `.min.css.map` existant ;
- ne jamais appeler `fs.writeFileSync()` en mode `--check` ;
- afficher une erreur claire si un fichier minifié manque ou diffère ;
- sortir avec code `1` en cas de diff.

Modifier `bin/minify-js.js` avec le même comportement pour `.min.js` et `.min.js.map`.

### 2. Conserver le mode génération

Sans `--check`, les scripts gardent le comportement actuel :

- écrire les fichiers minifiés ;
- écrire les source maps ;
- afficher les tailles et gains.

### 3. Ajouter les scripts npm

Modifier `package.json` :

```json
"build:css:check": "node bin/minify-css.js --check",
"build:js:check": "node bin/minify-js.js --check",
"build:check": "npm run build:css:check && npm run build:js:check"
```

Les scripts existants `build:css`, `build:js`, `build`, `build:clean` restent inchangés.

### 4. Modifier le Makefile

Changer :

```make
lint: build lint-js lint-css
```

en :

```make
lint: lint-js lint-css
```

Ajouter :

```make
build-check: ## Vérifier que les assets minifiés sont à jour sans les modifier
	@npm run build:check
```

Changer :

```make
test: phpcs phpunit lint
```

en :

```make
test: phpcs phpunit lint build-check
```

### 5. Vérifier l'absence de mutation

Après `make test`, `git status --short` ne doit pas montrer de modification générée par les assets si le dépôt était propre avant la commande.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `bin/minify-css.js` | Ajouter mode `--check` read-only |
| `bin/minify-js.js` | Ajouter mode `--check` read-only |
| `package.json` | Ajouter scripts `build:*:check` et `build:check` |
| `Makefile` | Retirer build de lint, ajouter build-check, mettre test en read-only |
| `doc/quick-reference.md` | Documenter `npm run build:check` et `make test` read-only |
| `doc/release/README.md` | Vérifier que la procédure de release demande toujours un build explicite |

## Sécurité et maintenance

- Les scripts de check ne doivent pas créer de fichiers temporaires dans le dépôt.
- Les erreurs doivent lister les fichiers obsolètes pour guider le développeur.
- Le mode génération reste explicite et volontaire.

## Hors périmètre

- Supprimer les fichiers minifiés du dépôt.
- Remplacer cssnano ou terser.
- Refactoriser les wrappers Docker npm.
- Modifier la stratégie de livraison de l'artefact ZIP.

## Tests et validation

### Tests automatisés

- [ ] `npm run build:check` passe quand les minifiés sont à jour.
- [ ] Modifier temporairement un fichier `.min.css` fait échouer `npm run build:css:check`.
- [ ] Modifier temporairement un fichier `.min.js` fait échouer `npm run build:js:check`.
- [ ] `npm run build` régénère les fichiers et fait repasser `npm run build:check`.
- [ ] `make test` passe.

### Validation read-only

- [ ] Partir d'un working tree propre.
- [ ] Lancer `make test`.
- [ ] Lancer `git status --short`.
- [ ] Vérifier qu'aucun fichier tracké n'a été modifié par la validation.
