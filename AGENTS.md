# L'Atelier Kiyose - Thème WordPress v2

## Contexte

L'Atelier Kiyose est un centre de bien-être et développement personnel situé à Brux (86510, Nouvelle-Aquitaine), animé par Virginie Le Mignon ("Happycultrice"). Le site propose 4 piliers de services:

1. **Art-thérapie** - Ateliers créatifs (théâtre, danse, arts plastiques, kintsugi)
2. **Rigologie / Yoga du rire** - Techniques psychocorporelles de libération émotionnelle
3. **Bols tibétains** - Sonothérapie et rééquilibrage énergétique
4. **Ateliers philosophie** - Débats démocratiques pour enfants et adolescents

**Public cible principal**: Femmes adultes (art-thérapie, rigologie, bols tibétains, ateliers créatifs)
**Public secondaire**: Enfants et adolescents (ateliers philosophie) — le ton et le design des pages philo doivent rester accessibles à ce public plus jeune

Site actuel: https://www.latelierkiyose.fr/
Ancien thème: https://github.com/latelierkiyose/wp-theme


## Documentation détaillée

**Langue de la documentation**: Toute la documentation est rédigée en français. Les termes techniques peuvent rester en anglais lorsqu'ils sont communément utilisés dans l'écosystème WordPress/web (exemples : overlay, CTA, design system, prepared statement, focus trap, lazy loading, callback, hook, template, etc.). **Éviter les sur-traductions** : ne pas traduire en français des termes techniques habituellement utilisés en anglais par les développeurs.

Pour plus d'informations, consulter les documents suivants dans `doc/`:

### Démarrage rapide
- **[Référence rapide](doc/quick-reference.md)** - Commandes essentielles, structure de fichiers, standards critiques

### Objectifs et spécifications
- **[Objectif de la refonte](doc/goal.md)** - Problèmes à résoudre et critères de succès
- **[Fonctionnalités](doc/features.md)** - Détail complet des pages et fonctionnalités
- **[Ton et voix](doc/tone.md)** - Positionnement, valeurs, style éditorial

### Architecture et standards
- **[Architecture](doc/architecture.md)** - Structure des fichiers, stack technique, templates, conventions
- **[Standards](doc/standards.md)** - WPCS, accessibilité WCAG 2.2 AA, sécurité, performance, tests
- **[Design System](doc/design-system.md)** - Palette de couleurs, typographie, composants UI, identité Kintsugi

### Intégrations et validation
- **[Plugins & Intégrations](doc/integrations.md)** - Events Manager, Brevo, CF7, SEO, sécurité
- **[Validation Checklist](doc/validation-checklist.md)** - Checklist complète pour validation des features et pré-production

### Spécifications produit (PRD)
- **[PRD](doc/prd/)** - Product Requirements Documents pour les fonctionnalités majeures
  - Format standardisé pour documenter les nouvelles fonctionnalités
  - Chaque PRD dans son propre répertoire: `prd/NNNN-nom-de-la-spec/`
  - Numérotation séquentielle à 4 chiffres (ex: 0001, 0002)
  - **Statut des PRDs**: Chaque PRD doit inclure une section "Statut" en en-tête avec l'un des états suivants:
    - `brouillon` - Document en cours de rédaction, non finalisé
    - `prêt à l'implémentation` - Spécification validée et raffinée, prête à être développée
    - `terminé` - Fonctionnalité implémentée et validée

### Références
- **[Exemples CSS](references/css-examples.css)** - Code CSS réutilisable (variables, composants, Kintsugi)
- **[Informations entreprise](references/business-info.json)** - Données de contact et informations légales
- **[Tests manuels](doc/tests-manuels.md)** - Guide complet pour tester le thème localement


## Environnement de développement local

### Démarrage rapide

**Prérequis** : Docker et Docker Compose installés. Aucune installation de PHP ou Composer n'est requise localement.

```bash
# Installer les dépendances
make install

# Installer les git hooks (pre-commit)
make install-hooks

# Démarrer WordPress
make start
```

Accès après démarrage :
- Site WordPress: `http://127.0.0.1:8000`
- Admin WordPress: `http://127.0.0.1:8000/wp-admin`
- PHPMyAdmin: `http://127.0.0.1:40001`

### Commandes disponibles

Pour voir toutes les commandes disponibles :
```bash
make help
```

**Commandes principales** :
- `make install` — Installer les dépendances Composer via Docker
- `make install-hooks` — Installer les git hooks (pre-commit avec PHPCS, ESLint, Stylelint)
- `make start` — Démarrer l'environnement WordPress
- `make stop` — Arrêter l'environnement
- `make phpcs` — Valider le code avec PHPCS
- `make phpcs-fix` — Corriger automatiquement les violations PHPCS
- `make logs` — Afficher les logs de WordPress
- `make clean` — Nettoyer les fichiers générés
- `make test` — Exécuter tous les tests (PHPCS)

### Scripts Docker (sans PHP local)

Le projet inclut des wrappers Docker pour exécuter les outils PHP sans installation locale :

- `./bin/composer.sh <commande>` — Exécuter Composer via Docker
- `./bin/phpcs.sh` — Exécuter PHPCS via Docker
- `./bin/phpcbf.sh` — Exécuter PHPCBF via Docker

**Exemples** :
```bash
# Installer une nouvelle dépendance
./bin/composer.sh require vendor/package

# Mettre à jour les dépendances
./bin/composer.sh update

# Valider le code
./bin/phpcs.sh --report=summary
```

### Tests manuels

Pour un guide complet des tests manuels, consulter **[doc/tests-manuels.md](doc/tests-manuels.md)**.

**Notes:**
- Le thème est monté dans le container et accessible immédiatement
- Les modifications de fichiers sont reflétées en temps réel (pas de rebuild nécessaire)
- Pour arrêter l'environnement: `make stop` ou `docker compose down`


## Architecture Technique

Pour la documentation complète de l'architecture, voir **[doc/architecture.md](doc/architecture.md)**.

### Résumé

**Type de thème**: Thème WordPress classique avec templates PHP (pas de Block Theme/FSE)

**Stack technique:**
- PHP 8.1+, WordPress 6.7+
- CSS natif moderne (variables, Grid, Flexbox)
- Vanilla JS ES6+

**Structure clé:**
```
latelierkiyose/
├── functions.php
├── style.css
├── templates/          # Page templates
├── inc/                # PHP modules
├── assets/             # CSS, JS, fonts, images
└── template-parts/     # Reusable parts
```

**Conventions:**
- Préfixe: `kiyose_` (fonctions, hooks)
- Text domain: `kiyose`
- CSS: Méthodologie BEM


## Standards & Contraintes

Pour la documentation complète des standards, voir **[doc/standards.md](doc/standards.md)** et **[doc/validation-checklist.md](doc/validation-checklist.md)**.

### Résumé des exigences critiques

**WordPress Coding Standards (WPCS):**
- Indentation: tabs (pas d'espaces)
- Nommage: `kiyose_` prefix pour tout
- Sécurité: Sanitize inputs, escape outputs, use nonces
- Validation: PHP_CodeSniffer + WordPress rulesets

**Accessibilité WCAG 2.2 AA (PRIORITÉ ABSOLUE):**
- Contraste: ≥4.5:1 (texte), ≥3:1 (UI)
- Navigation clavier complète
- Focus visible, landmarks ARIA
- Skip links, headings hiérarchiques
- Responsive 320px+, touch targets 44x44px

**Performance (Core Web Vitals):**
- LCP < 2.5s
- INP < 200ms
- CLS < 0.1

**Tests obligatoires:**
- PHPUnit pour fonctions critiques
- axe DevTools (0 violations)
- Navigation clavier manuelle
- Tests navigateurs (Chrome, Firefox, Safari, iOS, Android)


## Guide pour Agents AI

### Ordre de lecture recommandé

1. **Démarrage rapide**: [Référence rapide](doc/quick-reference.md) (commandes, structure, standards critiques)
2. **Objectifs**: [Objectif](doc/goal.md) (problèmes à résoudre, critères de succès)
3. **Architecture**: [Architecture](doc/architecture.md) (structure technique détaillée)
4. **Standards**: [Standards](doc/standards.md) (WPCS, accessibilité, sécurité)
5. **Design**: [Design system](doc/design-system.md) (palette, typographie, composants)
6. **Fonctionnalités**: [Fonctionnalités](doc/features.md) (pages et fonctionnalités détaillées)

### Avant de coder

- ✅ Vérifier les conventions (préfixe `kiyose_`, BEM pour CSS)
- ✅ Consulter la palette de couleurs et tester les contrastes
- ✅ Écrire les tests en premier (approche TDD)
- ✅ Respecter l'architecture modulaire (fichiers dans `inc/`)

### Checklist après chaque fonctionnalité

- [ ] Contraste WCAG 2.2 AA validé (≥4.5:1 texte, ≥3:1 UI)
- [ ] Navigation clavier testée (Tab, Entrée, Échap, flèches)
- [ ] Responsive 320px+ vérifié
- [ ] Tests PHPUnit passent (si applicable)
- [ ] Code respecte WPCS (tabulations, préfixes, sanitization/escaping)
- [ ] Focus visible sur tous les éléments interactifs
- [ ] Images avec `alt` approprié

### Commandes fréquentes

```bash
# Démarrage environnement
make start

# Validation WPCS
make phpcs

# Correction automatique PHPCS
make phpcs-fix

# Tests complets
make test

# Arrêt environnement
make stop
```

### Ressources rapides

- **Contraste**: [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)
- **Accessibilité**: axe DevTools extension
- **Performance**: Lighthouse (Chrome DevTools)
- **HTML**: [W3C Validator](https://validator.w3.org/)


