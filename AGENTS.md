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

## Checklist post-fonctionnalité

- [ ] Contraste WCAG 2.2 AA validé
- [ ] Navigation clavier testée (Tab, Entrée, Échap, flèches)
- [ ] Responsive 320px+ vérifié
- [ ] Focus visible sur tous les éléments interactifs
- [ ] `make test` passe
- [ ] Images avec `alt` approprié

## Documentation détaillée

Consulter à la demande avec l'outil Read (ne pas importer avec @) :

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
