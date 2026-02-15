# Référence rapide

## Commandes essentielles

### Environnement Docker
```bash
# Démarrer l'environnement WordPress
docker compose up

# Démarrer en arrière-plan (mode détaché)
docker compose up -d

# Arrêter l'environnement
docker compose down

# Voir les logs
docker compose logs -f

# Reconstruire les containers (si docker-compose.yaml a changé)
docker compose up --build
```

### Points d'accès
- **Site WordPress**: http://127.0.0.1:8000
- **Admin**: http://127.0.0.1:8000/wp-admin
- **Identifiants**: Configurés lors de la première installation

### Git hooks
```bash
# Installer les hooks pre-commit (PHPCS + ESLint + Stylelint)
make install-hooks

# Les hooks vérifient automatiquement le code à chaque commit
# Pour bypass (non recommandé):
git commit --no-verify
```

### Validation et tests
```bash
# Vérifier les standards WordPress (PHP)
./vendor/bin/phpcs --standard=WordPress latelierkiyose/

# Corriger automatiquement (quand possible)
./vendor/bin/phpcbf --standard=WordPress latelierkiyose/

# Valider JavaScript avec ESLint
npm run lint:js
# ou via Docker (si Node.js non installé)
./bin/eslint.sh 'latelierkiyose/assets/js/**/*.js'

# Valider CSS avec Stylelint
npm run lint:css
# ou via Docker
./bin/stylelint.sh 'latelierkiyose/**/*.css'

# Valider tout (PHP + JS + CSS)
make test

# Corriger automatiquement JS et CSS
make lint-fix

# Exécuter les tests unitaires PHPUnit
./vendor/bin/phpunit
```

### CI/CD
```bash
# Les commits/PR déclenchent automatiquement la CI (GitHub Actions)
# La CI exécute : PHPCS, ESLint, Stylelint, PHPUnit, build de l'artefact ZIP

# Télécharger un artefact de build (nécessite gh CLI)
gh run download {run-id} -n theme-build

# Créer un tag de release
git tag -a v1.0.0 -m "Release 1.0.0"
git push origin v1.0.0
```

**Important**: Seul l'artefact ZIP créé par la CI doit être déployé en production.

## Structure de fichiers (fichiers clés uniquement)

```
latelierkiyose/
├── functions.php          # Point d'entrée du thème
├── style.css             # Métadonnées du thème uniquement
├── header.php            # En-tête du site
├── footer.php            # Pied de page
│
├── templates/            # Templates de pages
│   ├── page-home.php
│   ├── page-contact.php
│   └── page-calendar.php
│
├── inc/                  # Modules PHP
│   ├── setup.php
│   ├── enqueue.php
│   └── custom-post-types.php
│
└── assets/
    ├── css/main.css      # Feuille de style principale
    └── js/main.js        # JavaScript principal
```

## Standards critiques

### Conventions de nommage
- **Fonctions PHP**: `kiyose_function_name()`
- **Hooks**: `kiyose_hook_name`
- **Domaine de texte**: `kiyose`
- **Classes CSS**: Méthodologie BEM

### Qualité du code
- **Indentation**: Tabulations (pas d'espaces)
- **Standards PHP**: WordPress Coding Standards (WPCS)
- **Sécurité**: Sanitize inputs, escape outputs, utiliser nonces

### Accessibilité (WCAG 2.2 AA)
- **Contraste**: ≥4.5:1 (texte), ≥3:1 (composants UI)
- **Clavier**: Navigation complète au clavier
- **Mobile**: Largeur minimale 320px
- **Cibles tactiles**: Minimum 44x44px

### Objectifs de performance
- **LCP**: < 2.5s
- **INP**: < 200ms
- **CLS**: < 0.1

### Exigences de tests
- **PHP**: PHPUnit pour les fonctions critiques
- **Accessibilité**: axe DevTools + tests clavier manuels
- **Navigateurs**: Chrome, Firefox, Safari (dernières versions)
- **Mobile**: iOS Safari, Chrome Android

## Palette de couleurs (accès rapide)

| Nom | Hex | Usage |
|------|-----|-------|
| Rose poudré | `#D7A4A4` | Primaire (boutons, liens) |
| Taupe rosé | `#C9ABA7` | Secondaire (arrière-plans) |
| Or miel | `#E6A528` | Accent (éléments Kintsugi) |
| Jaune doré | `#F4C975` | Accent clair (highlights) |
| Bordeaux foncé | `#5D0505` | Logo, titres importants |
| Beige clair | `#EFE5E4` | Arrière-plan principal |

> **Note contraste**: Certaines combinaisons de la palette nécessitent des ajustements pour conformité WCAG AA. Voir `doc/design-system.md` pour les détails.

## Typographie (accès rapide)

| Usage | Police | Graisses |
|-------|--------|----------|
| Titres (serif) | Lora | Regular (400), Bold (700), Italic (400i) |
| Corps (sans-serif) | Nunito | Regular (400), SemiBold (600), Bold (700) |

Auto-hébergées dans `assets/fonts/` (conformité RGPD).

## Workflow d'assignation de templates

1. Ajouter l'en-tête au fichier template:
   ```php
   <?php
   /*
   Template Name: Nom de votre template
   */
   ```

2. Dans l'admin WordPress:
   - Modifier la page → Attributs de page → Modèle
   - Sélectionner votre template depuis le menu déroulant
   - Publier/Mettre à jour

## Workflow de développement

1. **Avant de coder**:
   - Vérifier les conventions (préfixe, nommage)
   - Vérifier les contrastes de couleurs pour l'accessibilité
   - Écrire les tests en premier (approche TDD)

2. **Après chaque fonctionnalité**:
   - Tester la navigation au clavier
   - Valider le design responsive (320px+)
   - Exécuter les tests PHPUnit
   - Vérifier la conformité WPCS

3. **Avant de commiter**:
   - Exécuter tous les tests
   - Valider l'accessibilité (axe)
   - Vérifier la performance (Lighthouse)

4. **Déploiement en production**:
   - Pousser sur `main` → CI s'exécute automatiquement
   - Tests passent → Artefact ZIP créé
   - Télécharger l'artefact validé depuis GitHub Actions
   - Déployer UNIQUEMENT l'artefact ZIP (jamais directement depuis Git)

## Fonctions WordPress courantes

### Enqueue Assets
```php
wp_enqueue_style( 'kiyose-main', get_template_directory_uri() . '/assets/css/main.css' );
wp_enqueue_script( 'kiyose-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0', true );
```

### Sécurité
```php
// Sanitize
sanitize_text_field( $input );
esc_url( $url );

// Escape
esc_html( $text );
esc_attr( $attribute );

// Nonces
wp_nonce_field( 'action_name', 'nonce_field_name' );
wp_verify_nonce( $_POST['nonce_field_name'], 'action_name' );
```

## Liens vers la documentation complète

- **[Objectifs](goal.md)** - Objectifs du projet et critères de succès
- **[Architecture](architecture.md)** - Structure technique détaillée
- **[Standards](standards.md)** - WPCS, accessibilité, exigences de tests
- **[Fonctionnalités](features.md)** - Spécifications complètes des pages et fonctionnalités
- **[Design system](design-system.md)** - Couleurs, typographie, composants
- **[Ton](tone.md)** - Voix de la marque et messaging
- **[Intégrations](integrations.md)** - Plugins et services tiers
- **[Checklist de validation](validation-checklist.md)** - Checklist de tests et QA
