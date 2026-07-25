# PRD 0065 — Landing page « ressource gratuite »

- **Statut** : terminé (fondations) — la mise en page de la maquette reste à implémenter
- **Criticité** : moyenne
- **Version** : 1.0
- **Date** : 2026-07-25

## Contexte

L'Atelier Kiyose diffuse une ressource gratuite — « Le guide pour cultiver ta joie de vivre » — en échange d'une adresse e-mail. Cette page d'acquisition doit être hébergée sur le site WordPress, mais elle ne doit **pas ressembler au reste du site** : pas de menu de navigation, pas d'en-tête, pas de pied de page institutionnel, pas de décorations kintsugi. Une page verticale unique, orientée conversion, dont le seul chemin de sortie est le formulaire d'inscription.

Maquette cible : `docs/plans/2026-07-25-landing-page/LandingPageMockup.png`.

Le thème charge aujourd'hui son bundle CSS/JS (`kiyose-main`, `kiyose-header`, `kiyose-navigation`, `kiyose-footer`, `kiyose-kintsugi`, `kiyose-animations`, `kiyose-gutenberg-blocks`, `main.js`) **inconditionnellement** sur toutes les pages, via le registre déclaratif de `latelierkiyose/inc/enqueue.php`. Aucun mécanisme ne permet actuellement de rendre une page sans ce chrome : même en écrivant un template sans `get_header()`, les feuilles de style du site continueraient de s'appliquer.

Ce PRD couvre les **fondations** de ce template. La mise en page complète de la maquette fait l'objet d'une itération distincte.

## Objectif

1. Fournir un template de page assignable depuis l'éditeur, rendu sans en-tête, sans navigation et sans pied de page du site.
2. Couper ce template du bundle CSS/JS du thème, sans dégrader le chargement des autres pages.
3. Doter le template d'une feuille de style autonome portant son propre reset, son fond et son conteneur.
4. Préserver le niveau d'accessibilité du reste du site (WCAG 2.2 AA), malgré l'absence des styles globaux.
5. Permettre de retirer une landing page de l'index des moteurs de recherche, page par page.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Couleur de fond | Réutiliser `var(--kiyose-color-background)` | L'échantillonnage de la maquette donne exactement `#EFE5E4`, soit le token existant. Toute la palette de la maquette (`#F4C975`, `#E6A528`, `#5D0505`, `#D7A4A4`) est déjà composée de tokens du thème : la landing se distingue par l'absence de chrome et par sa mise en page, pas par ses couleurs |
| Isolation des assets | Liste blanche évaluée dans `kiyose_asset_condition_matches()` | Point de contrôle unique déjà traversé par toutes les décisions de chargement : aucune des déclarations d'assets existantes n'est modifiée, et un futur asset n'a pas à penser à s'exclure lui-même |
| Assets conservés | `kiyose-fonts`, `kiyose-variables`, `kiyose-landing` | `fonts.css` ne contient que des `@font-face` et `variables.css` que des custom properties : aucun style peint, aucune règle à neutraliser |
| JavaScript du thème | Aucun | La landing n'a ni menu mobile, ni overlay, ni animation au défilement |
| Wrappers | `header-landing.php` et `footer-landing.php`, chargés via `get_header( 'landing' )` / `get_footer( 'landing' )` | Convention WordPress native ; conserve `wp_head()` et `wp_footer()`, indispensables aux plugins (formulaire Brevo) et à la barre d'administration |
| Reset CSS | Recopié dans `landing.css` | `main.css` n'étant plus chargé, le `box-sizing`, les marges par défaut et surtout les styles du skip link doivent être portés par la feuille du template |
| Source du contenu | `the_content()` pour l'instant | Non structurant : le squelette `<main>` + conteneur reste identique que le contenu vienne plus tard de meta boxes ou de blocs Gutenberg. La décision est prise à l'itération « mise en page » |
| Indexation | Case à cocher par page (meta `kiyose_landing_noindex`) | Une landing peut être publique ou réservée à une campagne ; le choix appartient à la rédaction, pas au thème |

## Spécifications

### 1. Template de page

Créer `latelierkiyose/templates/page-landing.php`, déclaré `Template Name: Landing page`.

Règles :

- appelle `get_header( 'landing' )` et `get_footer( 'landing' )` ;
- rend un `<main id="main" class="landing" tabindex="-1">` contenant un conteneur `.landing__container` ;
- rend le contenu de la page via la boucle WordPress standard ;
- ne rend **pas** `template-parts/page-shapes-container.php` : les décorations kintsugi ne s'appliquent pas ;
- protège le fichier par `defined( 'ABSPATH' ) || exit;`.

### 2. Wrappers header et footer

Créer `latelierkiyose/header-landing.php` :

- doctype, `language_attributes()`, `charset`, `viewport` ;
- `wp_head()`, `body_class( 'landing-page' )`, `wp_body_open()` ;
- le skip link, réutilisé depuis `kiyose_get_skip_link()` ;
- **aucun** élément `<header>`, `wp_nav_menu()`, bouton hamburger, overlay de menu mobile ou `section-divider`.

Créer `latelierkiyose/footer-landing.php` :

- `wp_footer()`, fermeture de `<body>` et `<html>` ;
- aucun contenu éditorial : le lien « Pour en savoir plus… » de la maquette est du contenu, rédigé dans l'éditeur.

### 3. Isolation des assets

Modifier `latelierkiyose/inc/enqueue.php`.

Ajouts :

| Fonction | Rôle |
|---|---|
| `kiyose_is_landing_template()` | Prédicat de template, sur le modèle de `kiyose_is_contact_template()` |
| `kiyose_get_landing_style_handles()` | Liste blanche des handles de styles autorisés sur la landing |

Règles :

- dans `kiyose_asset_condition_matches()`, quand la requête courante utilise le template landing, seuls les assets de type `style` dont le handle figure dans la liste blanche sont chargés ; tout le reste, y compris les scripts du thème, est écarté ;
- aucune déclaration existante de `kiyose_get_theme_assets()` n'est modifiée ;
- déclarer `kiyose-landing` (`/assets/css/components/landing.css`) avec pour dépendances `kiyose-fonts` et `kiyose-variables`, et pour condition `kiyose_is_landing_template` ;
- ajouter le template landing à `kiyose_is_dedicated_page_template()`, pour que la page ne soit pas traitée comme une page générique ;
- le préchargement des polices (`kiyose_preload_fonts()`) reste actif, cohérent avec le maintien de `kiyose-fonts` ;
- la gestion des assets de plugins (`kiyose_dequeue_unused_plugin_assets()`) reste inchangée.

### 4. Feuille de style

Créer `latelierkiyose/assets/css/components/landing.css`.

Doit contenir, `main.css` n'étant plus chargé :

- un reset minimal : `box-sizing: border-box` sur `*`, `*::before`, `*::after` ; marges par défaut remises à zéro ;
- la contrainte de largeur `html, body { max-width: 100%; overflow-x: hidden; }` reprise de `main.css` (barre d'administration) ;
- les styles de `body` : fond `var(--kiyose-color-background)`, couleur `var(--kiyose-color-text)`, police de corps, taille de base 16 px, interlignage confortable ;
- `.landing__container` : conteneur centré, largeur maximale `var(--kiyose-landing-container-width)` fixée à `50rem`, marges internes latérales fluides ;
- les règles `.skip-link` et `.skip-link:focus`, recopiées depuis `components/header.css` ;
- un style de focus visible par défaut sur `:focus-visible` ;
- des images fluides (`max-width: 100%`).

Règles :

- toute nouvelle custom property est préfixée `--kiyose-` et déclarée dans `landing.css`, pas dans `variables.css` : elle est spécifique au template ;
- aucune media query n'est nécessaire à ce stade : la combinaison largeur maximale + marges internes couvre 320 px jusqu'au grand écran.

### 5. Retrait de l'indexation

Ajouter une meta box, visible uniquement sur les pages utilisant le template landing, avec une case à cocher unique « Ne pas indexer cette page ».

Règles :

- meta `kiyose_landing_noindex`, valeur `1` ou absente ;
- sauvegarde protégée par nonce, vérification `DOING_AUTOSAVE`, `current_user_can( 'edit_post' )` et contrôle du template assigné, sur le modèle de la meta box « photo de contact » ;
- côté frontend, un filtre `wp_robots` ajoute `noindex` et `follow` quand la case est cochée sur une page landing ;
- l'API `wp_robots` est native depuis WordPress 5.7 et utilisée par Yoast : aucune balise `<meta name="robots">` n'est écrite en dur.

### 6. Documentation

- `doc/user/02-choisir-un-template.md` : ajouter le template au tableau et une section dédiée décrivant l'absence de menu et de pied de page ainsi que la case « Ne pas indexer cette page ».
- `doc/release/content-migration.md` : signaler le nouveau template de page et le nouveau champ meta `kiyose_landing_noindex`.

### 7. Approches écartées

| Approche | Raison du rejet |
|---|---|
| Ajouter une condition d'exclusion sur chacun des assets inconditionnels | Invasif — une trentaine de déclarations à modifier — et fragile : chaque nouvel asset devrait penser à s'exclure |
| Laisser le bundle se charger puis le `wp_dequeue` à priorité haute | Dépend de l'ordre des hooks, et laisse passer les styles injectés hors du registre |
| Créer une couleur de fond spécifique à la landing | La maquette utilise exactement le fond du site ; introduire un token supplémentaire n'apporterait qu'une duplication |
| Rendre le template sans `wp_head()` / `wp_footer()` | Casserait le formulaire Brevo, la barre d'administration et tout plugin injectant des assets |
| Créer une extension ou un thème séparé pour la landing | Disproportionné pour une page, et compliquerait la maintenance et la mise en production |

## Accessibilité et sécurité

- Le skip link reste présent et fonctionnel : ses styles sont portés par `landing.css`, faute de quoi il serait visible en permanence.
- La cible `#main` porte `tabindex="-1"` pour recevoir le focus.
- Le contraste texte / fond est celui du reste du site : `#333333` sur `#EFE5E4`, ratio 10,43:1.
- La taille de police de base reste à 16 px, et le focus est visible sur tous les éléments interactifs.
- La page est utilisable de 320 px de large jusqu'au grand écran, sans défilement horizontal.
- La meta box est protégée par nonce, contrôle de capacité et vérification du template.
- La valeur du champ meta est un booléen normalisé, jamais rendue directement dans le HTML.

## Hors périmètre

Traité dans une itération ultérieure :

- la mise en page de la maquette : photo de couverture, titres, encart « Mon guide gratuit », visuel du guide, liste « Tu trouveras dans ce guide », section biographique, bloc de clôture ;
- l'intégration et le style du formulaire Brevo — le style `kiyose-brevo-override` sera écarté par la liste blanche, il faudra soit l'y ajouter, soit styler le formulaire dans `landing.css` ;
- la typographie spécifique de la maquette, dont les titres ne reprennent pas Dancing Script ;
- le suivi de conversion et les statistiques ;
- la livraison du fichier PDF lui-même ;
- toute page de remerciement après inscription.

Définitivement hors périmètre :

- rendre le fond ou la largeur du conteneur configurables depuis l'admin ;
- réutiliser ce template pour des pages autres que des landings de campagne.

## Tests et validation

### Tests automatisés

- [ ] Sur le template landing, `kiyose_asset_condition_matches()` retourne `false` pour `kiyose-main` (style et script), `kiyose-header`, `kiyose-navigation`, `kiyose-footer` et `kiyose-kintsugi`.
- [ ] Sur le template landing, elle retourne `true` pour `kiyose-fonts`, `kiyose-variables` et `kiyose-landing`.
- [ ] Hors du template landing, `kiyose-landing` n'est pas chargé et `kiyose-main` l'est toujours.
- [ ] `kiyose_is_dedicated_page_template()` retourne `true` sur le template landing.
- [ ] `landing.css` déclare `.landing__container`, `--kiyose-landing-container-width`, la règle `.skip-link` et le fond du `body`.
- [ ] Le filtre `wp_robots` ajoute `noindex` quand la meta est cochée sur une page landing.
- [ ] Le filtre `wp_robots` laisse la réponse inchangée quand la meta est absente ou que la page n'utilise pas le template.

### Validation manuelle

- [ ] Créer une page, lui assigner le template « Landing page », publier : ni en-tête, ni menu, ni bouton hamburger, ni pied de page du site.
- [ ] La vue source ne référence ni `main.css`, ni `header.css`, ni `navigation.css`, ni `footer.css`, ni `kintsugi.css`, ni `main.js` — seulement `fonts.css`, `variables.css` et `landing.css`.
- [ ] La page d'accueil et une page de service chargent toujours l'intégralité de leurs assets.
- [ ] Le contenu est centré, large de 800 px au maximum, et reste lisible à 320 px sans défilement horizontal.
- [ ] `Tab` depuis le haut de la page fait apparaître le skip link ; `Entrée` déplace le focus sur le contenu principal.
- [ ] Le focus est visible sur tous les liens du contenu.
- [ ] Cocher la case « Ne pas indexer cette page » ajoute `<meta name="robots" content="noindex, follow">` ; la décocher la retire.

### Validation finale

- [ ] `./bin/phpcs.sh` passe.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `make build` régénère `landing.min.css` et sa source map.
- [ ] `make test` passe.
- [ ] `doc/user/02-choisir-un-template.md` documente le nouveau template.
- [ ] `doc/release/content-migration.md` mentionne le template et le champ meta.
