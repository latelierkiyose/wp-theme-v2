# PRD 0067 — Typographie, largeurs et contrastes de la landing « ressource gratuite »

- **Statut** : terminé
- **Criticité** : moyenne
- **Version** : 1.0
- **Date** : 2026-07-25

## Contexte

PRD 0065 a coupé le template `templates/page-landing.php` du bundle CSS du thème (`main.css` n'y est pas chargé) pour isoler la page du chrome du site. PRD 0066 a ensuite posé la mise en page de la maquette (`.landing__columns`, `.landing__cta`, `.landing__photo--circle`) et le style du formulaire Brevo.

Conséquence non traitée par ces deux PRD : `main.css` portait aussi toute la typographie du site (titres, paragraphes, liens), et rien ne l'a remplacée dans `landing.css`. Si la rédaction assemble aujourd'hui la page selon la recette documentée, elle obtient des titres en Nunito gras gris `#333333` aux tailles par défaut du navigateur et des liens en bleu navigateur `#0000EE` — très éloigné de la maquette cible `docs/plans/2026-07-25-landing-page/LandingPageMockup.png`.

L'examen détaillé de la maquette face au code existant a par ailleurs fait remonter trois problèmes que PRD 0066 n'avait pas couverts, dont deux non-conformités WCAG 2.2 AA :

1. les titres dorés de la maquette, mesurés sur le fond beige du thème, sont très en dessous du contraste minimal ;
2. le bouton et la bordure de champ du formulaire Brevo, une fois posés sur le fond doré du bloc CTA (et non plus sur le fond beige pour lequel ils sont calibrés), échouent également au contraste ;
3. `.landing__columns > .wp-block-column { flex: 1; }` écrase les largeurs de colonnes que la rédaction règle dans l'éditeur, rendant certains ratios de la maquette inatteignables.

Ce PRD ferme ces écarts pour que la page soit réellement implémentable, sans revenir sur les fondations posées par PRD 0065 et PRD 0066.

## Objectif

1. Doter `landing.css` d'une typographie complète (titres, paragraphes, listes, liens), cohérente avec les tokens du thème.
2. Élargir le conteneur de la page à la largeur de la maquette et fournir les deux largeurs additionnelles nécessaires à son montage (colonne resserrée, bloc à fond perdu).
3. Corriger les trois défauts identifiés : contraste des titres dorés, contraste des composants Brevo dans le bloc CTA, respect des largeurs de colonnes réglées par la rédaction.
4. Conserver le niveau d'accessibilité WCAG 2.2 AA de tout le thème.
5. Étendre la recette d'assemblage documentée pour la rédaction en conséquence.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Police de titre de la maquette | Non reproduite ; conserver Dancing Script (titres) et Nunito (corps), les tokens existants du thème | La police display de la maquette appartient à Canva : le projet n'a pas de droits d'usage pour l'auto-héberger. Aucun nouvel asset, aucune licence à vérifier |
| Titres dorés de la maquette | Nouveau token `--kiyose-color-gold-dark: #8A5D00`, classe `.landing__title--gold` | Le doré de la maquette (`--kiyose-color-accent`, `#E6A528`) donne **1,74:1** sur le fond beige `#EFE5E4` — trois fois sous le seuil AA. `#8A5D00` donne **4,66:1**, conforme pour tout type de texte, y compris du texte courant |
| Emplacement du nouveau token | Section « VARIANTES DE CONTRASTE (WCAG 2.2 AA) » de `variables.css`, à côté de `--kiyose-color-primary-dark` | Ce n'est pas une propriété spécifique au template landing (contrairement à `--kiyose-landing-container-width`) mais une variante de palette assombrie pour raison de contraste, comme `--kiyose-color-primary-dark` : la section existe déjà pour ce cas exact. `variables.css` est whitelisté sur la landing, le token y est donc disponible sans modification de `kiyose_get_landing_style_handles()` |
| Couleur du corps de texte | `var(--kiyose-color-burgundy)` au lieu de `var(--kiyose-color-text)` | Fidèle à la maquette. Contraste **11,41:1** sur le fond beige, au-dessus des 10,23:1 actuels : aucune régression |
| Alignement du texte | `text-align: start` (comportement par défaut, non forcé) | La maquette justifie visuellement le texte, mais la justification est écartée : elle crée des espacements irréguliers qui gênent la lecture, en particulier pour les personnes dyslexiques (WCAG 1.4.8, critère AAA). Rien n'est justifié ailleurs sur le site |
| Couleur des liens au survol | Retrait de `color: var(--kiyose-color-primary)` ; renforcement du soulignement (`text-decoration-thickness`) à la place | `--kiyose-color-primary` (`#D7A4A4`) donne **1,63:1** sur le fond beige, non conforme dès qu'il porte une information d'état. Le corps de texte passant en bordeaux, un lien ne se distingue plus par la couleur : le soulignement, seul, porte l'information (WCAG 1.4.1) |
| Titre `h1::after` (soulignement décoratif doré) | Non repris dans `landing.css` | Purement décoratif dans `main.css`, absent de la maquette de la landing |
| Échelle de tailles des titres | `--kiyose-font-size-4xl` / `3xl` / `2xl` pour h1/h2/h3, contre `5xl` / `4xl` / `3xl` sur le reste du site | Dancing Script à la taille du site (`5xl`, 64px) serait disproportionné sur une page de conversion à colonne unique. Échelle resserrée d'un cran, cohérente avec les tokens existants — aucune nouvelle taille créée |
| Largeur du conteneur | `--kiyose-landing-container-width` porté de `50rem` à `64rem` (1024px) | Mesure de la largeur totale de la maquette. Bascule assumée : elle s'applique à toute landing existante ou future, pas seulement à celle-ci — cohérent avec le choix de PRD 0065 de ne pas rendre cette largeur configurable par page |
| Colonne resserrée | Nouveau token `--kiyose-landing-narrow-width: 34rem`, classe `.landing__narrow` | La maquette a des paragraphes plus étroits que le conteneur (ex. « Si tu es ici, c'est peut-être que… », face à « Bienvenue à l'Atelier Kiyose ! » qui occupe toute la largeur) |
| Fond perdu | Nouvelle classe `.landing__bleed` (`margin-inline: calc(50% - 50vw); max-width: 100vw;`) | La photo du hero déborde jusqu'aux bords de la fenêtre dans la maquette ; `.landing__container` ne le permet pas seul. `calc(50% - 50vw)` se résout sur la *content box* du conteneur (padding déjà déduit) : aucune compensation de padding nécessaire, vérifié à 320px et 1440px. Le débordement induit par `100vw` sur une fenêtre à barre de défilement classique est déjà masqué par `html, body { overflow-x: hidden }`, présent dans `landing.css` depuis PRD 0065 |
| Visuel de couverture du guide | Traité comme une image unique fournie par la rédaction (couverture + spirale + personnage + flèche + gribouillis) | Confirmé par la commanditaire : ces éléments font partie du PNG livré, pas de la mise en page. Aucun décor SVG, aucune classe d'inclinaison à ajouter au thème |
| `.landing__columns > .wp-block-column { flex: 1; }` | Remplacé par `flex-grow: 1;` seul | `flex: 1` vaut `flex: 1 1 0%` et écrase le `flex-basis` que Gutenberg pose en style inline quand la rédaction règle une largeur de colonne dans l'éditeur. `flex-grow: 1` seul laisse `flex-basis: auto`, qui respecte ce réglage tout en gardant le partage égal par défaut quand aucune largeur n'est choisie |
| Contraste des composants Brevo dans `.landing__cta` | Surcharge locale, scopée sous `.landing__cta`, avec `!important` | `brevo-override.css` calibre le bouton (fond `--kiyose-color-primary`, `#D7A4A4`) et la bordure de champ (`--kiyose-color-border`, `#C9ABA7`) pour un fond beige. Posés sur `--kiyose-color-gold-light` (`#F4C975`), ils tombent à 1,38:1 et 1,36:1 — sous le seuil 3:1 de WCAG 1.4.11 pour les composants d'interface. `!important` est nécessaire : `brevo-override.css` l'emploie déjà sur ces propriétés, la spécificité seule ne suffit pas à les surcharger |
| Couleur de bouton retenue dans `.landing__cta` | Fond bordeaux, texte blanc | `#5D0505` sur `#F4C975` = 9,04:1 pour le contour du composant ; texte blanc sur bordeaux = 14,1:1. Pas de nouveau token : réutilisation de `--kiyose-color-burgundy` et `--kiyose-color-white`, déjà whitelistés |
| `.landing__cta { text-align: center; }` | Retiré | La maquette aligne le premier encart CTA à gauche. La déclaration imposait un centrage non demandé ; l'alignement redevient un choix de la rédaction via les réglages du bloc Groupe |

## Spécifications

### 1. `latelierkiyose/assets/css/variables.css`

Ajouter, dans la section « VARIANTES DE CONTRASTE (WCAG 2.2 AA) », à la suite de `--kiyose-color-primary-dark` :

```css
/* Titres dorés sur fond beige — Ratio #8A5D00 sur #EFE5E4 = 4.66:1 ✅ (texte normal et grand texte) */
--kiyose-color-gold-dark: #8A5D00;
```

### 2. `latelierkiyose/assets/css/components/landing.css`

**Typographie**, nouvelle section après `BASE` et avant `LAYOUT` :

- `h1`–`h6` : `font-family: var(--kiyose-font-heading); color: var(--kiyose-color-burgundy); font-weight: 700; line-height: var(--kiyose-line-height-tight); margin-bottom: var(--kiyose-spacing-md); text-wrap: balance;` — sans le pseudo-élément `::after`.
- Tailles : `h1` → `--kiyose-font-size-4xl`, `h2` → `--kiyose-font-size-3xl`, `h3` → `--kiyose-font-size-2xl`, avec un point de rupture `@media (width <= 767px)` resserrant chacune d'un cran, sur le modèle de `main.css`.
- `.landing__title--gold { color: var(--kiyose-color-gold-dark); }`.
- `body` : `color` passe de `var(--kiyose-color-text)` à `var(--kiyose-color-burgundy)`.
- `p { margin-bottom: var(--kiyose-spacing-md); }`.
- `ul, ol { margin-bottom: var(--kiyose-spacing-md); padding-inline-start: var(--kiyose-spacing-lg); }`.
- `a { color: var(--kiyose-color-burgundy); text-decoration: underline; }`, `a:hover { text-decoration-thickness: 2px; }`, `a:focus-visible { border-radius: 2px; outline: 2px solid var(--kiyose-color-burgundy); outline-offset: 2px; }` (reprend le pattern déjà présent en bas du fichier pour `:focus-visible`, sans le dupliquer inutilement).

**Largeurs**, section `LAYOUT` :

- `--kiyose-landing-container-width` : `50rem` → `64rem`.
- Nouveau token `--kiyose-landing-narrow-width: 34rem;`.
- `.landing__narrow { max-width: var(--kiyose-landing-narrow-width); }`.
- `.landing__bleed { margin-inline: calc(50% - 50vw); max-width: 100vw; }`.

**Correctifs**, section `LAYOUT` :

- `.landing__columns > .wp-block-column { flex: 1; }` → `{ flex-grow: 1; }`.
- `.landing__cta` : retirer `text-align: center;`.
- `.landing__cta` : ajouter, scopé sous ce sélecteur, les surcharges des composants Brevo :

```css
.landing__cta form[id^="sib_signup_form_"] input[type="text"],
.landing__cta form[id^="sib_signup_form_"] input[type="email"],
.landing__cta form[id^="sib_signup_form_"] textarea {
	border-color: var(--kiyose-color-burgundy) !important;
}

.landing__cta form[id^="sib_signup_form_"] .sib-default-btn,
.landing__cta form[id^="sib_signup_form_"] input[type="submit"] {
	background-color: var(--kiyose-color-burgundy) !important;
	border-color: var(--kiyose-color-burgundy) !important;
	color: var(--kiyose-color-white) !important;
}

.landing__cta form[id^="sib_signup_form_"] .sib-default-btn:hover,
.landing__cta form[id^="sib_signup_form_"] input[type="submit"]:hover {
	background-color: #4a0404 !important;
	border-color: #4a0404 !important;
}
```

Le focus (`outline: 2px solid var(--kiyose-color-burgundy)`, déjà posé par `brevo-override.css`) reste inchangé : à l'extérieur du bouton, sur le fond doré, il conserve 9,04:1.

### 3. Template et assets

Aucun changement à `templates/page-landing.php`, `header-landing.php`, `footer-landing.php`, ni à `kiyose_get_landing_style_handles()` : les cinq feuilles déjà whitelistées (`fonts.css`, `variables.css`, `landing.css`, `plugins-common.css`, `brevo-override.css`) suffisent.

### 4. Documentation

`doc/user/02-choisir-un-template.md`, tableau « Assembler la mise en page » de la section Landing page : ajouter `landing__title--gold` (titres en doré foncé), `landing__narrow` (paragraphe resserré), `landing__bleed` (photo à fond perdu), et deux précisions :

- le visuel de couverture du guide est une image unique fournie par la commanditaire ; son `alt` doit restituer le texte qui y figure (« Le guide pour cultiver ta joie de vivre ») ;
- les largeurs de colonnes réglées dans l'éditeur (bloc Colonnes) sont désormais respectées par la mise en page.

`doc/release/content-migration.md` : aucun changement — aucune des cinq feuilles chargées sur la landing ne change de nom ni de rôle.

## Accessibilité et sécurité

- **Contraste des titres** : `--kiyose-color-gold-dark` (4,66:1) remplace l'usage direct de `--kiyose-color-accent` (1,74:1) pour tout texte doré sur la landing.
- **Contraste du corps** : bordeaux sur beige, 11,41:1, au-dessus du seuil actuel du thème.
- **Contraste des composants Brevo dans le bloc CTA** : bouton et bordure de champ passent de 1,38:1/1,36:1 à 9,04:1, conformes à WCAG 1.4.11 (composants d'interface, seuil 3:1).
- **Distinction des liens** : la couleur du corps de texte passant en bordeaux comme celle des liens, le soulignement devient le seul signal visuel distinguant un lien — renforcé au survol, conforme WCAG 1.4.1 (ne pas utiliser la couleur seule).
- **Focus visible** : inchangé sur les liens (`outline` bordeaux) et sur le bouton Brevo (`outline` bordeaux, 9,04:1 sur fond doré).
- **Cibles tactiles** : inchangées, déjà couvertes par `plugins-common.css`/`brevo-override.css` (PRD 0066).
- **Justification du texte** : délibérément non reproduite, pour ne pas dégrader la lisibilité (WCAG 1.4.8).
- **Fond perdu** : `.landing__bleed` ne modifie pas la largeur de défilement de la page — `html, body { overflow-x: hidden }` (PRD 0065) neutralise le débordement de `100vw` sur les navigateurs à barre de défilement visible.

## Hors périmètre

- Corriger `--kiyose-color-accent` utilisé en texte ailleurs sur le site (`.search-page__query`, `.event-price`) : même risque de contraste, hors du template landing, signalé mais non traité ici.
- Corriger `--kiyose-color-link`, référencé par `.sib-form__checkbox label a` dans `brevo-override.css` mais absent de `variables.css` : déclaration invalide pré-existante, hors du périmètre de ce PRD.
- Reproduire la police display de la maquette : bloqué par l'absence de droits d'usage.
- Décors SVG (flèche, gribouillis « www ») : intégrés à l'image de couverture fournie, pas au thème.
- Rendre `--kiyose-landing-container-width`/`--kiyose-landing-narrow-width` configurables par page (déjà écarté par PRD 0065).

## Tests et validation

### Tests automatisés

- [x] `variables.css` déclare `--kiyose-color-gold-dark: #8A5D00;`.
- [x] `landing.css` déclare, pour `h1`–`h6`, `font-family: var(--kiyose-font-heading);` et `color: var(--kiyose-color-burgundy);`.
- [x] `landing.css` déclare `.landing__title--gold` avec `color: var(--kiyose-color-gold-dark);`.
- [x] `landing.css` déclare `body { color: var(--kiyose-color-burgundy);` (mise à jour de l'assertion existante qui vérifiait `var(--kiyose-color-text)`).
- [x] `landing.css` déclare une règle `a {` avec `text-decoration: underline;`.
- [x] `landing.css` déclare `--kiyose-landing-container-width: 64rem;` et `--kiyose-landing-narrow-width: 34rem;`.
- [x] `landing.css` déclare `.landing__narrow` et `.landing__bleed` avec `calc(50% - 50vw)`.
- [x] `landing.css` déclare `.landing__columns > .wp-block-column { flex-grow: 1; }` et ne contient plus `flex: 1;` à cet endroit.
- [x] `landing.css` ne contient plus `text-align: center;` sous `.landing__cta`.
- [x] `landing.css` déclare, sous `.landing__cta`, une surcharge `background-color: var(--kiyose-color-burgundy) !important;` pour `input[type="submit"]`.

### Validation manuelle

- [ ] Créer une page réelle avec le template Landing, l'assembler avec les classes étendues (dont `landing__title--gold`, `landing__narrow`, `landing__bleed`), publier.
- [ ] Rendu 320px → grand écran sans défilement horizontal, y compris sur un navigateur à barre de défilement visible (Firefox/Windows).
- [ ] À partir de 768px, la photo du hero atteint le bord du viewport ; le texte reste dans les limites du conteneur.
- [ ] Une largeur de colonne réglée dans l'éditeur (bloc Colonnes) est effectivement respectée au rendu.
- [ ] Dans le bloc CTA : bouton bordeaux/texte blanc, bordure de champ bordeaux, focus clavier visible sur les deux formulaires.
- [ ] Aucun lien affiché en bleu par défaut du navigateur.
- [ ] Vue source : toujours seulement `fonts.css`, `variables.css`, `landing.css`, `plugins-common.css`, `brevo-override.css`.

### Validation finale

- [ ] `./bin/phpcs.sh` passe.
- [ ] `./bin/stylelint.sh` passe.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `make build` régénère `landing.min.css` et `variables.min.css` (et leurs source maps).
- [ ] `make test` passe.
- [ ] `doc/user/02-choisir-un-template.md` documente les classes étendues.
