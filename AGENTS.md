# L'Atelier Kiyose — Thème WordPress v2

Thème WordPress classique (PHP, pas de Block Theme/FSE) pour un centre de bien-être à Brux (86510). Public cible : femmes adultes + enfants/ados (ateliers philo).

## Conventions obligatoires

Les conventions normatives vivent dans `.claude/rules/` (chargé automatiquement à chaque session) :

- `.claude/rules/conventions.md` — préfixe `kiyose_`, text domain, `--kiyose-`, BEM, tabulations, JS
- `.claude/rules/wordpress.md` — sanitize/escape/nonces, `$wpdb->prepare()`, enqueue, templates
- `.claude/rules/accessibility.md` — WCAG 2.2 AA (priorité absolue), clavier, structure, touch

Seule règle qui n'y figure pas :

- **Langue de la documentation** : français ; termes techniques en anglais acceptés (overlay, hook, template, focus trap, etc.)

## Stack technique

- PHP 8.3+, WordPress 7.0+
- CSS natif moderne (variables, Grid, Flexbox)
- Vanilla JS ES6+

## Structure clé

```
latelierkiyose/
├── functions.php        # Point d'entrée
├── style.css            # Métadonnées uniquement
├── templates/           # Page templates
├── inc/                 # Modules PHP
├── assets/css|js|fonts  # Assets
└── template-parts/      # Composants réutilisables
```

## Commandes essentielles

```bash
make start          # Démarrer WordPress (Docker)
make stop           # Arrêter
make install        # Installer les dépendances Composer
make install-hooks  # Installer le hook pre-commit (PHPCS + ESLint + Stylelint)
make phpcs          # Valider WPCS
make phpcs-fix      # Corriger WPCS auto
make phpunit        # Tests PHPUnit
make lint-fix       # Corriger JS + CSS auto
make build          # Minifier les assets (CSS + JS)
make test           # phpcs + phpunit + lint + test-scripts + build-check
```

Wrappers Docker (pas besoin de PHP ni de Node local) : `./bin/phpcs.sh`, `./bin/phpcbf.sh`, `./bin/composer.sh`, `./bin/eslint.sh`, `./bin/stylelint.sh`, `./bin/phpunit.sh`, `./bin/prettier.sh`

## PRDs

Spécifications dans `doc/prd/NNNN-nom/`. Statuts : `brouillon` → `prêt à l'implémentation` → `terminé`.
Quand l'implémentation est terminée, toujours passer le statut à `terminé`.

## Documentation utilisateur

La doc utilisateur vit dans `doc/user/` (public cible : rédacteurices non-techniques). Quand un shortcode, une classe CSS réutilisable, un template de page, un CPT ou un paramètre exposé au rédacteur est ajouté, modifié ou supprimé, mettre à jour `doc/user/` dans le même changement. Fichiers concernés selon le type de modification :

- Shortcode → `doc/user/04-shortcodes.md` (+ recette dans `doc/user/recettes/` si nouveau cas d'usage)
- Classe CSS rédacteur → `doc/user/03-mise-en-page-avec-des-classes.md`
- Template de page → `doc/user/02-choisir-un-template.md`
- CPT ou meta field rédacteur → fichier dédié (`05-temoignages.md`, etc.)
- Fonctionnalité qui passe de "prêt" à "terminé" → retirer de `doc/user/a-venir.md` et ajouter dans la page de référence concernée

Ton : tutoiement, français, sans jargon non introduit. Toujours donner un exemple copiable.

## Documentation release

La procédure de mise en production vit dans `doc/release/`. Quand une modification impacte l'installation ou la configuration en production, mettre à jour `doc/release/` dans le même changement. Déclencheurs :

- Ajout/suppression d'un plugin requis (shortcode tiers consommé par un template) → `doc/release/plugins.md` + étape 3 de `doc/release/README.md`
- Ajout/suppression d'un CPT, d'un meta field ou d'une option attendue côté admin → `doc/release/content-migration.md`
- Nouveau template de page, nouveau menu location ou nouvelle taille d'image → `doc/release/content-migration.md`
- Nouveau hook d'activation (`after_switch_theme`…) ou nouvelle constante `wp-config.php` requise → étape 5 de `doc/release/README.md`
- Changement de la chaîne de build qui modifie l'artefact livré → étape 4 de `doc/release/README.md`
- Changement de version minimale PHP/WordPress → prérequis de `doc/release/README.md`

## Checklist post-fonctionnalité

- [ ] Contraste WCAG 2.2 AA validé
- [ ] Navigation clavier testée (Tab, Entrée, Échap, flèches)
- [ ] Responsive 320px+ vérifié
- [ ] Focus visible sur tous les éléments interactifs
- [ ] `make test` passe
- [ ] Images avec `alt` approprié
- [ ] `doc/user/` mis à jour si l'évolution est visible côté rédaction
- [ ] `doc/release/` mis à jour si la mise en production est impactée

## Deux arborescences de documentation

Ne pas confondre :

- **`doc/`** — toute la documentation éditoriale et de référence : `prd/`, `user/`, `release/` et les fichiers listés ci-dessous. **C'est ici qu'on écrit par défaut.**
- **`docs/`** — deux artefacts hérités, hors du flux courant : `docs/plans/` (documents de conception ponctuels) et `docs/design-system/` (export HTML statique du design system, avec ses polices et son logo). Ne rien y ajouter sans raison explicite.

## Documentation détaillée

Consulter à la demande avec l'outil Read (ne pas importer avec @) :

- `doc/user/README.md` — Documentation pour les rédacteurices (public non-technique)
- `doc/release/README.md` — Procédure de mise en production (plugins requis, migration contenu, rollback)
- `doc/quick-reference.md` — Commandes, structure, standards critiques
- `doc/architecture.md` — Structure technique détaillée
- `doc/standards.md` — WPCS, accessibilité, sécurité, performance
- `doc/design-system.md` — Palette, typographie, composants UI, identité Kintsugi
- `doc/features.md` — Pages et fonctionnalités détaillées
- `doc/goal.md` — Objectifs et critères de succès
- `doc/tone.md` — Positionnement, valeurs, style éditorial
- `doc/integrations.md` — Events Manager, Brevo, CF7, SEO
- `doc/validation-checklist.md` — Checklist de validation pré-production
- `references/css-examples.css` — Variables et composants CSS réutilisables
- `references/business-info.json` — Coordonnées et informations légales
