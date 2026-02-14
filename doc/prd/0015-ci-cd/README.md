# PRD 0015 — CI/CD et déploiement

## Statut

**brouillon**

## Objectif

Mettre en place le pipeline d'intégration continue (GitHub Actions) pour valider automatiquement le code (WPCS, PHPUnit) et produire un artefact de build déployable.

## Dépendances

- **PRD 0001** : `composer.json`, `phpcs.xml` en place.

## Note

Ce PRD peut être implémenté dès la fin du PRD 0001 pour valider automatiquement les étapes suivantes. Il n'a pas besoin d'attendre la fin de tous les templates.

## Fichiers à créer

```
wp-theme-v2/
└── .github/
    └── workflows/
        └── ci.yml                 # Pipeline CI GitHub Actions
```

## Spécifications détaillées

### Pipeline CI (`.github/workflows/ci.yml`)

**Déclencheurs** :
- Push sur `main` et `develop`
- Pull requests vers `main`

**Job 1 : Tests** (`test`)

Étapes :
1. Checkout du code
2. Setup PHP 8.1
3. `composer install`
4. Exécuter PHPCS : `./vendor/bin/phpcs`
5. Exécuter PHPUnit : `./vendor/bin/phpunit` (si des tests existent)

**Job 2 : Build** (`build`)

Conditions :
- Dépend du job `test` (exécuté seulement si les tests passent)
- Uniquement sur push vers `main` (pas sur les PR)

Étapes :
1. Checkout du code
2. Créer l'artefact ZIP :
   - Inclure tout le contenu de `latelierkiyose/`
   - Exclure : `tests/`, fichiers de dev (`.gitignore` dans le thème)
3. Nommer l'artefact : `latelierkiyose-{sha}.zip`
4. Upload comme artefact GitHub Actions (rétention 30 jours)

**Contenu du YAML** : Reprendre la structure décrite dans `doc/architecture.md` (section CI/CD).

### Workflow de déploiement

Le déploiement en production n'est pas automatisé (pas de CD). Le processus est :

1. Code poussé sur `main` → CI s'exécute
2. Tests passent → Artefact ZIP créé
3. Télécharger l'artefact : `gh run download {run-id} -n theme-build`
4. Upload SFTP vers le serveur de production
5. Extraction dans `wp-content/themes/`
6. Vérification du site

### Versioning

- Suivre Semantic Versioning (SemVer) : `MAJOR.MINOR.PATCH`
- Version dans `style.css` (header du thème)
- Tags Git pour les releases : `git tag -a v1.0.0 -m "Release 1.0.0"`

## Tests et validation

### Tests automatisés
- [ ] Le workflow CI s'exécute sur un push vers `main`
- [ ] Le workflow CI s'exécute sur une PR vers `main`
- [ ] Le job `test` exécute PHPCS et PHPUnit
- [ ] Le job `build` crée un artefact ZIP valide
- [ ] Le job `build` ne s'exécute que sur push vers `main` (pas sur PR)

### Tests manuels
- [ ] L'artefact ZIP peut être téléchargé depuis GitHub Actions
- [ ] L'artefact ZIP contient le thème complet sans fichiers de dev
- [ ] L'artefact ZIP peut être installé comme thème WordPress (upload via admin)

## Hors périmètre

- Déploiement automatique (CD)
- Environnement de staging
- Tests d'intégration (navigateur)
