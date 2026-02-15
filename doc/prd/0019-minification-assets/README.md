# PRD 0019 — Minification des assets pour production

**Statut**: prêt à l'implémentation  
**Créé le**: 15 février 2026  
**Dépendances**: PRD 0001 (Squelette), PRD 0015 (CI/CD)

## Problème

Actuellement, le thème sert des fichiers CSS et JavaScript **non minifiés** en production :

- ❌ Fichiers CSS volumineux avec commentaires, indentation, espaces
- ❌ Fichiers JavaScript volumineux avec commentaires, espaces
- ❌ Temps de chargement et bande passante augmentés inutilement
- ❌ Non-conformité avec les standards documentés (doc/standards.md:421-423, doc/validation-checklist.md:81-82)
- ❌ Impact négatif sur Core Web Vitals (LCP, performance Lighthouse)

**Référence standards** :
- `doc/standards.md:421-423` : "Minification en production: Enlever commentaires et espaces"
- `doc/validation-checklist.md:81-82` : "CSS minifié en production" + "JavaScript minifié en production"

## Solution

Implémenter un **processus de build automatisé** qui :

1. **Minifie tous les CSS et JS** pendant le build CI/CD
2. **Génère des fichiers `.min.css` et `.min.js`** dans les mêmes dossiers que les sources
3. **Enqueue les versions minifiées** sur le site de production
4. **Exclut les fichiers sources non minifiés** du ZIP de production

**+ Recommandation plugin** : Maintenir la recommandation d'Autoptimize/WP Rocket dans le guide de déploiement pour optimisations supplémentaires (combinaison de fichiers inter-plugins, cache, etc.)

## Approche technique

### 1. Outils de minification

**CSS** : cssnano + postcss-cli
- Enlève commentaires, espaces, optimise les règles
- Préserve les variables CSS

**JavaScript** : terser
- Enlève commentaires, espaces, raccourcit les noms de variables
- Préserve la compatibilité ES6

### 2. Scripts npm

Ajouter à `package.json` :

```json
{
  "scripts": {
    "build:css": "postcss latelierkiyose/assets/css/**/*.css --use cssnano --ext .min.css --no-map",
    "build:js": "terser latelierkiyose/assets/js/**/*.js -o latelierkiyose/assets/js/**/*.min.js --compress --mangle",
    "build": "npm run build:css && npm run build:js",
    "build:clean": "find latelierkiyose/assets -name '*.min.css' -delete && find latelierkiyose/assets -name '*.min.js' -delete"
  }
}
```

**Note** : Le script `build:css` va créer des fichiers `.min.css` à côté de chaque fichier `.css` source. Idem pour `build:js`.

### 3. Workflow GitHub Actions

Modifier `.github/workflows/build.yml` dans le job `build` :

```yaml
- name: Setup Node.js
  uses: actions/setup-node@v4
  with:
    node-version: '18'
    cache: 'npm'

- name: Install npm dependencies
  run: npm ci

- name: Build minified assets
  run: npm run build

- name: Copy theme files
  run: |
    rsync -av --exclude-from=.github/workflows/build-exclude.txt latelierkiyose/ build/latelierkiyose/
```

### 4. Enqueue conditionnel en production

Modifier `inc/setup.php` (fonction `kiyose_enqueue_scripts()`) :

```php
function kiyose_enqueue_scripts() {
	$is_production = ! defined( 'WP_DEBUG' ) || ! WP_DEBUG;
	$suffix        = $is_production ? '.min' : '';

	// Styles
	wp_enqueue_style(
		'kiyose-variables',
		get_template_directory_uri() . "/assets/css/variables{$suffix}.css",
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// JavaScript
	wp_enqueue_script(
		'kiyose-navigation',
		get_template_directory_uri() . "/assets/js/navigation{$suffix}.js",
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
```

**Logique** :
- En **développement local** (`WP_DEBUG = true`) : charge `navigation.js` (source non minifiée, facilite le debug)
- En **production** (`WP_DEBUG = false`) : charge `navigation.min.js` (minifié, optimisé)

### 5. ZIP artifact contents

**Decision**: Include **both** source and minified files in the production ZIP.

**Rationale**:
- Simpler rsync exclusion rules (no complex pattern matching required)
- Source files useful for debugging production issues
- PHP enqueue logic ensures minified files are served in production (`WP_DEBUG = false`)
- Source files served in development/staging (`WP_DEBUG = true`)
- Minimal size impact (~10-15% larger ZIP, acceptable tradeoff)

**Result**: Production ZIP contains both `.css` + `.min.css`, `.js` + `.min.js`.

## Bénéfices attendus

### Performance
- ✅ **Réduction de 30-50%** de la taille des fichiers CSS/JS
- ✅ **LCP amélioré** (moins de bytes à télécharger)
- ✅ **Bande passante économisée** pour les visiteurs mobile
- ✅ **Score Lighthouse Performance** amélioré

### Conformité
- ✅ Respect des standards documentés (doc/standards.md, doc/validation-checklist.md)
- ✅ Conformité Core Web Vitals (LCP < 2.5s)

### Maintenabilité
- ✅ Développement sur fichiers sources lisibles (non minifiés)
- ✅ Production sert automatiquement les versions optimisées
- ✅ Processus automatisé (zéro action manuelle)

## Tests de validation

### Tests build local

```bash
# Installer les dépendances
npm install

# Nettoyer les fichiers minifiés existants
npm run build:clean

# Lancer le build
npm run build

# Vérifier que les fichiers .min.css et .min.js existent
find latelierkiyose/assets -name "*.min.css" | head -5
find latelierkiyose/assets -name "*.min.js" | head -5

# Vérifier la taille des fichiers
ls -lh latelierkiyose/assets/css/main.css latelierkiyose/assets/css/main.min.css
ls -lh latelierkiyose/assets/js/navigation.js latelierkiyose/assets/js/navigation.min.js
```

**Critères de succès** :
- [ ] Tous les fichiers `.css` ont un équivalent `.min.css`
- [ ] Tous les fichiers `.js` ont un équivalent `.min.js`
- [ ] Fichiers `.min` sont **30-50% plus petits** que les sources
- [ ] Contenu des fichiers `.min` est valide (pas de syntaxe cassée)

### Tests CI/CD

```bash
# Déclencher un build GitHub Actions
git add .
git commit -m "feat: add asset minification"
git push origin main

# Attendre la fin du build
gh run list --limit 1

# Télécharger l'artefact
gh run download $(gh run list --limit 1 --json databaseId -q '.[0].databaseId')

# Extraire et vérifier
unzip -l latelierkiyose-theme-*.zip | grep ".min.css"
unzip -l latelierkiyose-theme-*.zip | grep ".min.js"

# Vérifier que les fichiers sources NON minifiés sont absents
unzip -l latelierkiyose-theme-*.zip | grep -E "(navigation\.js|main\.css)" | grep -v "\.min\."
```

**Critères de succès** :
- [ ] ZIP contient uniquement les fichiers `.min.css` et `.min.js`
- [ ] ZIP **ne contient pas** les fichiers sources `.css` et `.js` (sauf exception configuration)
- [ ] Taille du ZIP réduite de ~20-30%

### Tests fonctionnels production

Déployer le thème sur un site de test avec `WP_DEBUG = false` :

1. Activer le thème
2. Inspecter le HTML source (View Source)
3. Vérifier que les `<link>` et `<script>` pointent vers `.min.css` et `.min.js`
4. Ouvrir DevTools > Network
5. Vérifier que les fichiers chargés sont bien les versions minifiées
6. Vérifier que le site fonctionne normalement (navigation, carousel, formulaires)

**Critères de succès** :
- [ ] Tous les styles s'appliquent correctement
- [ ] Tous les scripts fonctionnent (navigation mobile, carousel, etc.)
- [ ] Aucune erreur Console JavaScript
- [ ] Aucune erreur 404 pour les fichiers `.min`

### Tests Lighthouse

Avant/après comparaison :

```bash
# Avant (fichiers non minifiés)
lighthouse https://test.latelierkiyose.fr/ --only-categories=performance

# Après (fichiers minifiés)
lighthouse https://test.latelierkiyose.fr/ --only-categories=performance
```

**Critères de succès** :
- [ ] Score Performance augmenté de **+5 à +10 points**
- [ ] LCP réduit de **100-300ms**
- [ ] "Minify CSS" et "Minify JavaScript" ne sont plus listés dans les opportunités

## Critères d'acceptance

- [x] Documentation PRD complète
- [ ] Dépendances npm installées (cssnano, postcss-cli, terser)
- [ ] Scripts npm fonctionnels (`build:css`, `build:js`, `build`, `build:clean`)
- [ ] Workflow GitHub Actions modifié pour exécuter `npm run build`
- [ ] `inc/setup.php` modifié pour enqueue conditionnel `.min` en production
- [ ] `.github/workflows/build-exclude.txt` mis à jour pour exclure fichiers sources
- [ ] Tests locaux validés (fichiers `.min` générés et valides)
- [ ] Tests CI/CD validés (ZIP contient uniquement `.min`)
- [ ] Tests fonctionnels validés (site fonctionne avec fichiers minifiés)
- [ ] Documentation mise à jour (deployment-guide.md, validation-checklist.md, standards.md)
- [ ] Lighthouse score amélioré (performance +5 minimum)

## Fichiers impactés

### Nouveaux fichiers
- `doc/prd/0019-minification-assets/README.md` (ce document)

### Fichiers modifiés
- `package.json` — Ajout des scripts de build et dépendances
- `.github/workflows/build.yml` — Ajout de l'étape `npm run build`
- `latelierkiyose/inc/setup.php` — Enqueue conditionnel `.min` en production
- `.github/workflows/build-exclude.txt` — Exclusion des fichiers sources du ZIP

### Documentation mise à jour
- `doc/deployment-guide.md` — Mention de la minification automatique
- `doc/validation-checklist.md` — Cocher "CSS/JS minifié en production"
- `doc/standards.md` — Préciser le processus de build
- `AGENTS.md` — Ajouter PRD 0019 au statut du projet

## Plan de rollback

En cas de problème avec les fichiers minifiés :

1. **Rollback immédiat** : Modifier `inc/setup.php` pour forcer `$suffix = ''` (désactive minification)
2. **Debug local** : Comparer fichiers `.min` et sources, identifier la casse
3. **Fix** : Corriger la configuration de minification et redéployer

## Références

- **Standards** : doc/standards.md:421-423
- **Validation** : doc/validation-checklist.md:81-82
- **CI/CD** : PRD 0015
- **cssnano** : https://cssnano.co/
- **terser** : https://terser.org/
- **PostCSS** : https://postcss.org/
