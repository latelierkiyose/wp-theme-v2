# L'Atelier Kiyose - Thème WordPress v2

## Statut du projet

**État actuel**: Phase de configuration initiale
- Structure du thème: À créer
- Fichiers implémentés: Aucun
- Prochaines étapes: Implémenter la structure de base du thème selon l'architecture définie

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

**Langue de la documentation**: Toute la documentation est rédigée en français. Les termes techniques peuvent rester en anglais lorsqu'ils sont communément utilisés dans l'écosystème WordPress/web (exemples : overlay, CTA, design system, prepared statement, focus trap, lazy loading, callback, hook, template, etc.).

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

### Références
- **[Exemples CSS](references/css-examples.css)** - Code CSS réutilisable (variables, composants, Kintsugi)
- **[Informations entreprise](references/business-info.json)** - Données de contact et informations légales


## Environnement de développement local

### Lancer WordPress en local

Le projet utilise Docker Compose pour démarrer un environnement WordPress complet en local.

**Commande:**
```sh
docker compose up
```

**Accès:**
- Site WordPress: `http://127.0.0.1:8000`
- Interface d'administration: `http://127.0.0.1:8000/wp-admin`
- Credentials: Configurés lors de la première installation (voir variables d'environnement dans `docker-compose.yaml` si applicable)

**Notes:**
- Le thème est monté dans le container et accessible immédiatement
- Les modifications de fichiers sont reflétées en temps réel (pas de rebuild nécessaire)
- Pour arrêter l'environnement: `docker compose down`


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
5. **Design**: [Système de design](doc/design-system.md) (palette, typographie, composants)
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
docker compose up

# Validation WPCS
./vendor/bin/phpcs --standard=WordPress latelierkiyose/

# Tests PHPUnit
./vendor/bin/phpunit

# Arrêt environnement
docker compose down
```

### Ressources rapides

- **Contraste**: [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)
- **Accessibilité**: axe DevTools extension
- **Performance**: Lighthouse (Chrome DevTools)
- **HTML**: [W3C Validator](https://validator.w3.org/)


