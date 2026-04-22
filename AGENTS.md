# L'Atelier Kiyose — Thème WordPress v2

Thème WordPress classique (PHP, pas de Block Theme/FSE) pour un centre de bien-être à Brux (86510). Public cible : femmes adultes + enfants/ados (ateliers philo).

## Conventions obligatoires

- **Préfixe PHP** : `kiyose_` pour toutes les fonctions, hooks, handles
- **Text domain** : `kiyose`
- **CSS custom properties** : préfixe obligatoire `--kiyose-` (Stylelint pattern `^kiyose-.+`)
- **CSS** : méthodologie BEM
- **Indentation** : tabulations (pas d'espaces) — WPCS
- **Sécurité** : sanitize inputs, escape outputs, use nonces
- **Accessibilité** : WCAG 2.2 AA — priorité absolue (contraste ≥4.5:1 texte, ≥3:1 UI)
- **Langue de la documentation** : français ; termes techniques en anglais acceptés (overlay, hook, template, focus trap, etc.)

## Stack technique

- PHP 8.3+, WordPress 6.7+
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
make phpcs          # Valider WPCS
make phpcs-fix      # Corriger WPCS auto
make test           # Tous les tests (PHPCS + linters)
make lint-fix       # Corriger JS + CSS auto
```

Wrappers Docker (pas besoin de PHP local) : `./bin/phpcs.sh`, `./bin/phpcbf.sh`, `./bin/composer.sh`

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
