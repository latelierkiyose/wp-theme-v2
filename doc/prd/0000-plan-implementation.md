# Plan d'implémentation - Thème L'Atelier Kiyose v2

## Statut

**terminé**

## Vue d'ensemble

Remplacement du thème WordPress existant par un thème v2 entièrement recodé. Le contenu (pages, articles) reste en base de données ; seul le thème change. La migration se fait manuellement (réassignation des templates aux pages existantes après activation).

## Hypothèses et décisions

- **Plugins existants conservés** : Events Manager et Brevo restent en place. CF7 sera installé.
- **Migration manuelle** : Pas de script de migration. Après activation du thème, les templates sont réassignés aux pages dans l'admin WordPress.
- **Témoignages** : Actuellement en dur dans une page. Un CPT `kiyose_testimony` sera créé pour les gérer proprement. Les témoignages existants seront ressaisis manuellement dans le CPT.
- **Polices** : Lora et Nunito à télécharger (Google Fonts) et auto-héberger dans `assets/fonts/`.

## Séquence d'implémentation

Chaque étape est un PRD autonome, reviewable et testable indépendamment. Les dépendances sont indiquées.

### Phase 1 — Fondations (infrastructure du thème)

| PRD | Titre | Dépendances | Description |
|-----|-------|-------------|-------------|
| [0001](0001-squelette-theme/) | Squelette du thème | Aucune | Structure de fichiers, `style.css`, `functions.php`, `inc/setup.php`, `inc/enqueue.php`, Composer, PHPCS |
| [0002](0002-design-tokens/) | Design tokens et polices | 0001 | Variables CSS, `@font-face`, téléchargement des polices, `variables.css`, `fonts.css` |
| [0003](0003-layout-navigation/) | Layout et navigation desktop | 0001, 0002 | `header.php`, `footer.php`, skip link, navigation principale, landmarks ARIA |
| [0004](0004-menu-mobile/) | Menu mobile | 0003 | Hamburger, overlay, focus trap, JS `mobile-menu.js` |

### Phase 2 — Templates de base (pages essentielles)

| PRD | Titre | Dépendances | Description |
|-----|-------|-------------|-------------|
| [0005](0005-templates-base/) | Templates de base | 0003 | `index.php`, `page.php`, `404.php`, `search.php` |
| [0006](0006-blog/) | Blog | 0005 | `archive.php`, `single.php`, `template-parts/content-blog.php`, pagination |
| [0007](0007-cpt-temoignages/) | CPT Témoignages | 0001 | Déclaration CPT, meta boxes, `template-parts/content-testimony.php` |

### Phase 3 — Templates spécifiques (pages de contenu)

| PRD | Titre | Dépendances | Description |
|-----|-------|-------------|-------------|
| [0008](0008-page-accueil/) | Page d'accueil | 0005, 0006, 0007 | `templates/page-home.php` avec hero, 4 piliers, prochaines dates, témoignages, actualités |
| [0009](0009-template-services/) | Template services | 0005 | `templates/page-services.php` — template générique pour les 4 piliers |
| [0010](0010-page-contact/) | Page contact | 0005 | `templates/page-contact.php`, installation et intégration CF7 |
| [0011](0011-page-a-propos/) | Page à propos | 0005 | `templates/page-about.php` |
| [0012](0012-page-calendrier/) | Calendrier & tarifs | 0005 | `templates/page-calendar.php`, styling Events Manager |

### Phase 4 — Intégrations et finitions

| PRD | Titre | Dépendances | Description |
|-----|-------|-------------|-------------|
| [0013](0013-integrations-plugins/) | Intégrations plugins | 0003, 0008 | Styling Brevo newsletter (footer + homepage), styling CF7, styling Events Manager |
| [0014](0014-kintsugi-finitions/) | Kintsugi et finitions visuelles | 0002, 0003 | Séparateurs dorés, overlays vitrail, bulles décoratives, animations/transitions |

### Phase 5 — Mise en production

| PRD | Titre | Dépendances | Description |
|-----|-------|-------------|-------------|
| [0015](0015-ci-cd/) | CI/CD et déploiement | 0001 | GitHub Actions, build artefact ZIP, pipeline WPCS + PHPUnit |
| [0016](0016-activation-production/) | Activation et pré-production | Toutes | Checklist complète, activation du thème, réassignation templates, vérifications finales |

## Critères de validation par étape

Chaque PRD inclut sa propre checklist de validation. Les critères communs sont :

1. **WPCS** : `phpcs --standard=WordPress` passe sans erreur sur les fichiers modifiés
2. **Tests** : PHPUnit passe (si applicable)
3. **Accessibilité** : Navigation clavier fonctionnelle, contrastes conformes, ARIA correct
4. **Responsive** : Rendu correct de 320px à 1920px
5. **Visuellement** : Résultat vérifiable dans le navigateur via `docker compose up`

## Notes

- L'ordre des phases 1-2 est strict (dépendances techniques).
- Les PRDs de la phase 3 peuvent être implémentés en parallèle une fois la phase 2 terminée.
- Le PRD 0015 (CI/CD) peut être implémenté dès la fin de la phase 1 pour valider automatiquement les étapes suivantes.
- Le PRD 0014 (Kintsugi) est volontairement en fin de parcours : c'est du polish qui peut être ajusté après le contenu.
