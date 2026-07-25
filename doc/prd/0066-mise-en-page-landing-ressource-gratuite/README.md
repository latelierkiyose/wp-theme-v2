# PRD 0066 — Mise en page de la landing page « ressource gratuite »

- **Statut** : terminé
- **Criticité** : moyenne
- **Version** : 1.0
- **Date** : 2026-07-25

## Contexte

PRD 0065 a construit les **fondations** de la landing page « ressource gratuite » : un template de page coupé du chrome et du bundle CSS/JS du thème (`templates/page-landing.php`, `header-landing.php`, `footer-landing.php`, isolation via `kiyose_get_landing_style_handles()`), une feuille de style autonome (`landing.css`) contenant un reset minimal et `.landing__container`, et une meta box de désindexation. Il a explicitement différé « la mise en page de la maquette » et « l'intégration et le style du formulaire Brevo » à une itération suivante — c'est l'objet de ce PRD.

Maquette cible : `docs/plans/2026-07-25-landing-page/LandingPageMockup.png`. Page verticale à colonne unique pour « Le guide pour cultiver ta joie de vivre » : une section hero (photo + texte d'accueil), une section offre (liste à puces + visuel du guide), un encart d'appel à l'action en forme de « blob » doré contenant un formulaire d'inscription — répété deux fois — une section biographique, et une ligne de clôture.

Toute la palette de la maquette correspond déjà aux tokens existants du thème (constaté par PRD 0065) : rien de nouveau à créer côté couleurs. La mise en page n'a pas besoin d'être pixel-perfect : un débordement de texte dans l'encart jaune sur la maquette source n'est pas à reproduire.

## Objectif

1. Fournir à la rédaction un petit ensemble fixe de classes CSS pour assembler la maquette avec les blocs natifs de Gutenberg (Colonnes, Groupe, Image, Titre, Bloc court-code).
2. Rendre le formulaire d'inscription Brevo utilisable et accessible sur ce template (cibles tactiles, focus visible, contraste), sans réintroduire le bundle du thème.
3. Documenter la recette d'assemblage pour la rédaction, cohérente avec le modèle « 100 % contenu » déjà acté par PRD 0065.
4. Conserver le niveau d'accessibilité WCAG 2.2 AA de tout le thème.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Modèle de contenu | Inchangé : `the_content()`, 100 % Gutenberg | Cohérent avec PRD 0065 ; aucune donnée structurée nouvelle à modéliser pour une page qui change rarement |
| Bloc pour les sections deux-colonnes (hero, bio) | Bloc **Colonnes** natif de Gutenberg | Comportement colonne/empilement natif et familier pour la rédaction, qui touchera cette page rarement ; évite d'imposer une classe flex sur un Groupe imbriqué à chaque édition |
| Classes CSS ajoutées | `.landing__hero`, `.landing__offer`, `.landing__cta`, `.landing__bio`, `.landing__columns` (primitive partagée empilement mobile / côte-à-côte ≥ 768 px), `.landing__photo--circle` | Petit set fixe de classes spécifiques au template, documentées dans la section « Landing page » existante de `doc/user/02-choisir-un-template.md` — pas dans le catalogue général `03-mise-en-page-avec-des-classes.md`, puisqu'elles ne sont utilisables que sur ce template |
| Réutilisation de `.grid`/`.grid--2-cols` ou du style Gutenberg « Ronde » | Rejetée | Ces classes vivent dans `main.css`/`gutenberg-blocks.css`, absents de la liste blanche landing ; les réintroduire casserait l'isolation voulue par PRD 0065 pour gagner une seule classe utilitaire |
| Couleur du bloc CTA « blob » | Fond `var(--kiyose-color-gold-light)`, texte `var(--kiyose-color-burgundy)` | Paire déjà utilisée et testée par `.kiyose-cta` (PRD 0061) : contraste 9,04:1, aucun nouveau risque de contraste à valider |
| Forme du blob | `border-radius` asymétrique (une seule déclaration CSS), pas de SVG/clip-path | Non pixel-perfect explicitement accepté ; un `border-radius` suffit à évoquer une forme organique sans marquage ni JS supplémentaires |
| Photo circulaire de la section bio | Nouvelle classe locale `.landing__photo--circle` dans `landing.css` | Le style « Ronde » de Gutenberg vit dans `gutenberg-blocks.css`, absent de la landing : il ne produirait aucun effet s'il était documenté par erreur. Reprend le pattern déjà existant `.about-overlay__image`/`.home-about__image` |
| Style du formulaire Brevo | Ajout de `kiyose-plugins-common` et `kiyose-brevo-override` à `kiyose_get_landing_style_handles()` | Option retenue explicitement par PRD 0065 plutôt que réécrire les styles de formulaire dans `landing.css` ; ces deux feuilles ne stylent que les sélecteurs `.sib-form`/`form[id^="sib_signup_form_"]`, sans interférence avec le reste de la page. Sans elles, les champs/bouton du formulaire natif du plugin restent sans les cibles tactiles 44 px ni le focus visible du thème |
| Titre de page (h1) | Aucun changement de template | `header-landing.php` ne rend aucun titre ; le premier bloc Titre rédigé dans l'éditeur (mis manuellement au niveau H1) fait office de h1. Imposer un h1 depuis le PHP contredirait le modèle « 100 % contenu » déjà acté |

## Spécifications

### 1. Feuille de style `assets/css/components/landing.css`

Ajout, à la suite de la section `LAYOUT` existante, en mobile-first avec un seul point de rupture `@media (width >= 768px)` :

- `.landing__hero`, `.landing__offer`, `.landing__cta`, `.landing__bio` : rythme vertical commun (`margin-block-start`) entre les sections empilées.
- `.landing__columns` : `display: flex; flex-direction: column;` en base (empilé) ; à partir de 768 px, `flex-direction: row; align-items: center;` avec `.landing__columns > .wp-block-column { flex: 1; }` pour un partage égal des deux colonnes.
- `.landing__cta` : fond `var(--kiyose-color-gold-light)`, texte `var(--kiyose-color-burgundy)`, `border-radius` asymétrique, padding généreux, texte centré.
- `.landing__photo--circle` : conteneur `aspect-ratio: 1 / 1; border-radius: 50%; overflow: hidden;` et son `img` en `object-fit: cover`.

### 2. Isolation des assets — formulaire Brevo

Modifier `latelierkiyose/inc/enqueue.php`, fonction `kiyose_get_landing_style_handles()` : ajouter `kiyose-plugins-common` et `kiyose-brevo-override` à la liste blanche. Ces deux handles dépendent déjà uniquement de handles déjà whitelistés (`kiyose-variables`, puis `kiyose-plugins-common`), donc la chaîne de dépendances WordPress se résout sans autre changement. `kiyose_asset_condition_matches()` n'a pas besoin d'être modifiée : la branche landing itère déjà génériquement `kiyose_get_landing_style_handles()`.

### 3. Template

Aucun changement à `templates/page-landing.php`, `header-landing.php` ou `footer-landing.php` : le modèle « 100 % contenu » de PRD 0065 est conservé tel quel.

### 4. Documentation

- `doc/user/02-choisir-un-template.md`, sous-section « Landing page » existante : recette d'assemblage (quel bloc pour quelle section, classes CSS exactes, avertissement sur le style « Ronde », instruction de niveau H1 manuel, duplication du bloc CTA).
- `doc/release/content-migration.md`, section 12 : liste des feuilles désormais chargées sur la landing (`landing.css`, `plugins-common.css`, `brevo-override.css`), y compris dans la checklist finale du fichier.

## Accessibilité et sécurité

- **Hiérarchie de titres** : un seul h1 (titre de la section hero, mis en niveau H1 manuellement), puis des h2 à plat pour « Mon guide gratuit… », l'intitulé de chaque bloc CTA, et « Si tu me rencontre ici… ».
- **Formulaires Brevo dupliqués** : le même shortcode `[sibwp_form id=…]` est rendu deux fois sur une même page. Le plugin Brevo n'étant pas vendorisé dans ce dépôt, on ne peut pas garantir statiquement qu'il suffixe les `id` HTML par instance ; un doublon d'`id` casserait l'association label/champ pour l'une des deux instances, indépendamment de tout CSS. Aucune correction ARIA n'est possible depuis le contenu (le bloc Court-code n'expose pas de champ `aria-labelledby`, et un filtre PHP contredirait le modèle « 100 % contenu »). Mitigation acceptée : donner un intitulé de texte différent aux deux blocs CTA, pour qu'un usage au clavier/lecteur d'écran distingue les deux formulaires même sans association programmatique.
- **Cibles tactiles** : le bouton de soumission Brevo hérite de `min-height`/`min-width: 44px` via `plugins-common.css`/`brevo-override.css` une fois whitelistés.
- **Contraste** : uniquement des tokens déjà validés (bordeaux sur or clair 9,04:1 pour le CTA ; texte sur fond 10,43:1 déjà couvert par PRD 0065).
- **Skip link / focus visible** : déjà couverts par PRD 0065, non affectés par ce PRD.

## Hors périmètre

- Réutiliser `.landing__columns`/`.landing__cta` en dehors de ce template.
- Corriger par PHP le doublon potentiel d'`id` HTML entre les deux formulaires Brevo — limitation documentée, pas corrigée dans ce PRD.
- Suivi de conversion, page de remerciement, livraison du fichier PDF (déjà hors périmètre de PRD 0065).

## Tests et validation

### Tests automatisés

- [x] `landing.css` déclare `.landing__columns`, `display: flex;`, `flex-direction: column;` et un bloc `@media (width >= 768px)`.
- [x] `landing.css` déclare `.landing__cta` avec `background-color: var(--kiyose-color-gold-light);` et `color: var(--kiyose-color-burgundy);`.
- [x] `landing.css` déclare `.landing__photo--circle` avec `border-radius: 50%;` et `object-fit: cover;`.
- [x] Sur le template landing, `kiyose_get_landing_style_handles()` inclut `kiyose-plugins-common` et `kiyose-brevo-override` (couvert dynamiquement par le test existant de whitelist).

### Validation manuelle

- [ ] Créer une page réelle avec le template Landing, l'assembler avec les nouvelles classes en suivant la recette documentée, publier.
- [ ] Rendu 320 px → grand écran sans défilement horizontal.
- [ ] Bascule colonne unique → deux colonnes à 768 px pour hero, offre et bio.
- [ ] Contraste du bloc CTA conforme, focus clavier visible sur les deux formulaires.
- [ ] Vue source : seules `fonts.css`, `variables.css`, `landing.css`, `plugins-common.css`, `brevo-override.css` sont chargées.
- [ ] Absence d'`id` HTML dupliqué entre les deux formulaires Brevo ; les deux sont utilisables indépendamment au clavier.

### Validation finale

- [ ] `./bin/phpcs.sh` passe.
- [ ] `./bin/stylelint.sh` passe.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `make build` régénère `landing.min.css` et sa source map.
- [ ] `make test` passe.
- [ ] `doc/user/02-choisir-un-template.md` documente la recette d'assemblage.
- [ ] `doc/release/content-migration.md` mentionne les nouvelles feuilles chargées.
