---
description: WordPress security and coding standards
globs:
  - "latelierkiyose/**/*.php"
---

# WordPress Coding Standards

## Sécurité (OWASP / WordPress)
- **Sanitize inputs** : `sanitize_text_field()`, `absint()`, `sanitize_email()`, etc.
- **Escape outputs** : `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- **Nonces** : `wp_nonce_field()` / `wp_verify_nonce()` pour tous les formulaires
- **Prepared statements** : `$wpdb->prepare()` pour toutes les requêtes SQL directes
- Ne jamais utiliser `$_GET`/`$_POST` sans sanitization

## Enqueue
- Toujours utiliser `wp_enqueue_style()` / `wp_enqueue_script()` — jamais de `<link>` ou `<script>` en dur
- Handle prefix : `kiyose-*`

## Templates
- Type : thème classique avec templates PHP (pas de Block Theme / FSE)
- Page templates dans `templates/`
- Composants réutilisables dans `template-parts/`
- Modules PHP dans `inc/`

## Validation
- `make phpcs` (ou `./bin/phpcs.sh`) avant chaque commit
- Le pre-commit hook exécute PHPCS + ESLint + Stylelint automatiquement
