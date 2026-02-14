# L'Atelier Kiyose - Thème WordPress v2

## Statut du projet

**État actuel**: PRD 0011 terminé — Page À propos implémentée
- Squelette du thème: ✅ Opérationnel (PRD 0001)
- Design tokens: ✅ Implémentés (PRD 0002)
  - Variables CSS: couleurs, typographie, espacements, animations
  - Polices auto-hébergées: Lora (serif) et Nunito (sans-serif)
  - Reset CSS et styles de base
  - Contraste WCAG 2.2 AA validé
- Layout et navigation: ✅ Implémentés (PRD 0003)
  - Header avec logo et navigation principale
  - Footer avec réseaux sociaux, liens légaux et copyright
  - Skip link pour accessibilité
  - Landmarks ARIA (banner, navigation, main, contentinfo)
  - Dropdowns accessibles au clavier avec ARIA
  - Responsive desktop (≥1024px)
- Menu mobile: ✅ Implémenté (PRD 0004)
  - Bouton hamburger avec animation en croix
  - Menu slide-in depuis la droite avec overlay
  - Focus trap complet (Tab/Shift+Tab)
  - Fermeture par Esc, clic overlay, ou bouton fermer
  - Verrouillage du scroll body
  - Respect de prefers-reduced-motion
  - Navigation ARIA complète
- Templates de base: ✅ Implémentés (PRD 0005)
  - page.php: Template page par défaut avec contenu centré
  - 404.php: Page d'erreur avec recherche et suggestions de pages
  - search.php: Résultats de recherche paginés
  - searchform.php: Formulaire de recherche accessible
  - index.php: Template fallback simplifié
  - Styles CSS pour tous les composants
- Blog: ✅ Implémenté (PRD 0006)
  - archive.php: Liste d'articles avec pagination (responsive: 1 col mobile, 2 cols tablet, 3 cols desktop)
  - single.php: Article unique avec image à la une, partage social (Facebook, LinkedIn), navigation entre articles
  - template-parts/content-blog.php: Carte d'article réutilisable
  - Commentaires désactivés sur les posts
  - Styles CSS: blog-card.css, blog-archive.css, blog-single.css
- CPT Témoignages: ✅ Implémenté (PRD 0007)
  - Custom Post Type `kiyose_testimony` avec meta box contexte
  - template-parts/content-testimony.php: Carte de témoignage réutilisable
  - Fonction helper kiyose_get_testimony_context_label()
  - Styles CSS: testimony.css avec bordure dorée et guillemets décoratifs
  - Tests PHPUnit: test-custom-post-types.php
- Shortcode Témoignages: ✅ Implémenté (PRD 0007b)
  - Shortcode `[kiyose_testimonials]` avec attributs: display, limit, context, columns
  - Mode grille: Responsive (1-4 colonnes) avec Grid CSS
  - Mode carousel: Défilement automatique accessible WCAG 2.2 AA
    - Autoplay avec pause/play (démarrage automatique si pas de prefers-reduced-motion)
    - Navigation clavier (flèches gauche/droite)
    - Boutons Previous/Next/Pause accessibles (44x44px minimum)
    - ARIA roles complets (region, group, aria-live, aria-hidden)
    - Transitions CSS fluides avec support prefers-reduced-motion
    - JavaScript ES6 module (KiyoseCarousel class)
  - Filtrage par contexte (individual, enterprise, structure)
  - Styles CSS: testimonials-grid.css, carousel.css
  - Enqueue conditionnel des styles (charge uniquement si shortcode présent)
  - Tests PHPUnit: test-shortcodes.php
  - Note technique: Utilise des vérifications if explicites au lieu de l'optional chaining `?.` pour compatibilité navigateurs
- Page d'accueil: ✅ Implémentée (PRD 0008)
  - Template: templates/page-home.php (Template Name: "Page d'accueil")
  - Section Hero: Tagline, sous-titre, CTA, overlay vitrail
  - Section 4 Piliers: Grille responsive de cartes de services (1 col mobile → 2 tablet → 4 desktop)
  - Section Prochaines dates: Intégration Events Manager avec placeholder
  - Section Témoignages: Carousel accessible réutilisant KiyoseCarousel
  - Section Newsletter: Placeholder pour formulaire Brevo (PRD 0013)
  - Section Actualités: 3 derniers articles du blog avec grille responsive (1 col mobile → 3 desktop)
  - Template part: template-parts/content-service.php (carte de service cliquable)
  - Styles CSS: hero.css, service-card.css, home-sections.css
  - Carousel CSS chargé conditionnellement sur page d'accueil
  - WCAG 2.2 AA: Hiérarchie h1→h2→h3, landmarks ARIA, navigation clavier
- Template services: ✅ Implémenté (PRD 0009)
  - Template: templates/page-services.php (Template Name: "Page de service")
  - Template générique pour les 4 pages de services (art-thérapie, rigologie, bols tibétains, ateliers philosophie)
  - Structure: Header avec titre + image hero optionnelle, contenu Gutenberg, footer avec CTAs
  - CTAs: Boutons "Me contacter" et "Voir le calendrier" en bas de page
  - Styles CSS: service-page.css avec styles pour blocs Gutenberg (titres, paragraphes, listes, images, citations, etc.)
  - Enqueue conditionnel (charge uniquement sur pages utilisant le template)
  - Image à la une utilise la taille 'kiyose-hero' (1920x800)
  - Boutons accessibles (44x44px minimum, focus visible)
  - Responsive: Layout centré (max-width: 900px), CTAs empilés sur mobile
  - WCAG 2.2 AA: Contrastes validés, navigation clavier, hiérarchie sémantique
- Page de contact: ✅ Implémentée (PRD 0010)
  - Template: templates/page-contact.php (Template Name: "Page de contact")
  - Layout 2 colonnes: Formulaire (2/3) + Informations de contact (1/3) en desktop, empilé en mobile
  - Formulaire Contact Form 7 intégré via the_content()
  - Informations de contact: Téléphone (tel:), email (mailto:), adresse physique
  - Liens réseaux sociaux: Facebook, Instagram, LinkedIn avec SVG icons
  - Styles CSS: contact-page.css, cf7-override.css
  - Override complet Contact Form 7: champs, boutons, messages d'erreur, validation
  - Enqueue conditionnel (charge uniquement sur page-contact.php)
  - Sticky sidebar sur desktop
  - WCAG 2.2 AA: Contrastes validés (≥4.5:1), focus visible, targets 44x44px, labels visibles
  - Note: Installation de Contact Form 7 requise (hors périmètre PRD)
- Page À propos: ✅ Implémentée (PRD 0011)
  - Template: templates/page-about.php (Template Name: "À propos")
  - Structure simple: Header avec titre + contenu Gutenberg
  - Contenu géré dans l'éditeur: Introduction, philosophie Kintsugi, parcours, qualifications
  - Styles CSS: about-page.css avec support complet Gutenberg
  - Blocs stylés: Colonnes (photo + texte), images, listes, citations, séparateurs
  - Images avec coins arrondis et ombre légère
  - Enqueue conditionnel (charge uniquement sur page-about.php)
  - Layout aéré et centré (max-width: 900px)
  - Responsive: Colonnes empilées sur mobile, images adaptatives
  - WCAG 2.2 AA: Hiérarchie sémantique, contrastes validés, navigation clavier
- Configuration: ✅ PHPCS, Composer, CI/CD GitHub Actions
- Prochaines étapes: Page Calendrier (PRD 0012)


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


