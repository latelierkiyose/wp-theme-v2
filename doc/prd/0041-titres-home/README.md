# PRD 0041 — Titres sur la home : orphelins de ponctuation

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-04-22

## Contexte

Sur la page d'accueil, certains titres (et éléments typographiquement équivalents) laissent un caractère orphelin sur la dernière ligne — typiquement la ponctuation forte française (`?`, `!`, `:`, `;`) qui, en l'absence d'espace insécable, peut être reportée seule à la ligne par le moteur de rendu.

Exemple observé :

```
 _____________________
| Mais où est Charlie |
| ?                   |
|_____________________|
```

Rendu cible :

```
 _____________________
| Mais où est         |
| Charlie ?           |
|_____________________|
```

Éléments concernés sur la home :

| Élément | Fichier:ligne | Nature | Risque |
|---|---|---|---|
| `<h1 class="welcome-block__title">` | `page-home.php:89` | Dynamique (post meta `kiyose_welcome_title`) | Élevé |
| `<p class="welcome-block__subtitle">` | `page-home.php:91` | Dynamique | Élevé |
| `<p class="welcome-block__slogan home-emphase">` | `page-home.php:119` | Dynamique (slogan entouré de « ») | Élevé |
| `<p class="home-content1__question">` | `page-home.php:176` | Dynamique — stylé comme titre (font-heading) | Très élevé (finit toujours par `?`) |
| `<p class="home-content1__slogan home-emphase">` | `page-home.php:185` | Dynamique | Élevé |
| `<p class="home-content2__slogan home-emphase">` | `page-home.php:217` | Dynamique | Élevé |
| h2 statiques (`home-about__title`, `home-events__title`, `home-testimonials__title`, `home-blog__title`, `newsletter-overlay__title`) | `page-home.php` | Statique | Nul |

Aucune utilité d'équilibrage typographique n'existe aujourd'hui dans le thème (pas de `text-wrap: balance`, pas d'espace insécable injectée — confirmé par exploration).

## Objectif

1. Supprimer les orphelins de ponctuation sur les titres et éléments titres-like de la home.
2. Appliquer la règle typographique française (espace fine insécable avant `?`, `!`, `:`, `;`) sur le contenu dynamique à risque.
3. Généraliser le rééquilibrage visuel (`text-wrap: balance`) à tous les `h1`–`h6` du thème — coût marginal nul, bénéfice étendu, cohérent avec le PRD 0038 qui a déjà globalisé l'esthétique des `h1`.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Rééquilibrage des lignes | `text-wrap: balance` sur `h1`–`h6` et `.home-emphase` | Propriété CSS native conçue pour cet usage. Support Chrome 114+, Safari 17.5+, Firefox 121+ (universel en 2026). Dégradation gracieuse vers `normal` sur anciens navigateurs |
| Portée CSS | Globale sur les `h*`, ciblée sur `.home-emphase` (slogans centrés, largeur 700px — rééquilibrage renforce l'équilibre visuel) | `.home-content1__question` n'est pas rééquilibré en CSS : le helper PHP `kiyose_fr_nbsp()` traite déjà le cas critique (`?` en fin de question) et le rééquilibrage global sur cet élément peut générer des coupures peu naturelles sur texte long |
| Ponctuation française | Helper PHP `kiyose_fr_nbsp()` insérant `&#8239;` (U+202F, espace fine insécable) avant `?`, `!`, `:`, `;` précédés d'une espace | Règle typographique française standard. `wptexturize()` ne le fait pas de façon fiable. U+202F est préféré à `&nbsp;` (U+00A0) : largeur optique plus juste avant ponctuation forte |
| Cible du helper | Uniquement les champs dynamiques de la home listés dans le tableau « éléments concernés » | Cible chirurgicale ; pas de risque de régression sur les paragraphes body où un retour de ponctuation est acceptable |
| Échappement | `kiyose_fr_nbsp()` s'applique **après** `esc_html()` et retourne du HTML contenant une entité numérique. Sortie via `echo` direct sans ré-échappement | L'entité `&#8239;` est sûre. Le contenu source est déjà échappé en amont |
| Emplacement du helper | `inc/content-filters.php` | Fichier existant dédié aux filtres de contenu (pattern déjà établi avec `kiyose_auto_add_heading_ids`) |

## Spécifications

### 1. CSS — `latelierkiyose/assets/css/main.css`

Ajouter `text-wrap: balance` au bloc groupé `h1, h2, h3, h4, h5, h6` (l. 60-71) :

```css
h1,
h2,
h3,
h4,
h5,
h6 {
	color: var(--kiyose-color-burgundy);
	font-family: var(--kiyose-font-heading);
	font-weight: 700;
	line-height: var(--kiyose-line-height-tight);
	margin-bottom: var(--kiyose-spacing-md);
	text-wrap: balance;
}
```

### 2. CSS — `latelierkiyose/assets/css/components/home-sections.css`

Ajouter `text-wrap: balance` au sélecteur `.home-emphase` (l. 174) :

```css
.home-emphase {
	color: var(--kiyose-color-burgundy);
	font-family: var(--kiyose-font-body);
	font-size: var(--kiyose-font-size-xl);
	font-weight: 600;
	line-height: var(--kiyose-line-height-tight);
	margin-left: auto;
	margin-right: auto;
	max-width: 700px;
	position: relative;
	text-align: center;
	text-wrap: balance;
}
```

### 3. PHP — `latelierkiyose/inc/content-filters.php`

Ajouter la fonction helper :

```php
/**
 * Inserts a French thin non-breaking space (U+202F) before high punctuation marks.
 *
 * Apply to dynamic title-like content only. Input MUST already be escaped
 * via esc_html(); output contains a numeric HTML entity and is safe to echo directly.
 *
 * @param string $text Already-escaped title text.
 * @return string Text with thin NBSP inserted before ?, !, :, ; when preceded by a space.
 */
function kiyose_fr_nbsp( $text ) {
	return preg_replace( '/ ([?!:;])/u', '&#8239;$1', $text );
}
```

### 4. PHP — `latelierkiyose/templates/page-home.php`

Wrapper des 6 sorties dynamiques à risque :

| Ligne | Avant | Après |
|---|---|---|
| 89 | `<?php echo esc_html( $kiyose_welcome_title ); ?>` | `<?php echo kiyose_fr_nbsp( esc_html( $kiyose_welcome_title ) ); ?>` |
| 91 | `<?php echo esc_html( $kiyose_welcome_subtitle ); ?>` | `<?php echo kiyose_fr_nbsp( esc_html( $kiyose_welcome_subtitle ) ); ?>` |
| 119 | `<?php echo esc_html( $kiyose_welcome_slogan ); ?>` | `<?php echo kiyose_fr_nbsp( esc_html( $kiyose_welcome_slogan ) ); ?>` |
| 176 | `<?php echo esc_html( $kiyose_qa_item['question'] ); ?>` | `<?php echo kiyose_fr_nbsp( esc_html( $kiyose_qa_item['question'] ) ); ?>` |
| 185 | `<?php echo esc_html( $kiyose_content1_slogan ); ?>` | `<?php echo kiyose_fr_nbsp( esc_html( $kiyose_content1_slogan ) ); ?>` |
| 217 | `<?php echo esc_html( $kiyose_content2_slogan ); ?>` | `<?php echo kiyose_fr_nbsp( esc_html( $kiyose_content2_slogan ) ); ?>` |

### 5. Tests unitaires — `latelierkiyose/tests/Test_Content_Filters.php` (nouveau)

Cas à couvrir pour `kiyose_fr_nbsp()` :

- `kiyose_fr_nbsp_ofEmptyString_returnsEmptyString`
- `kiyose_fr_nbsp_ofTextWithoutPunctuation_returnsUnchanged`
- `kiyose_fr_nbsp_ofQuestionMark_insertsNbspBefore` → `'Charlie ?'` → `'Charlie&#8239;?'`
- `kiyose_fr_nbsp_ofExclamation_insertsNbspBefore`
- `kiyose_fr_nbsp_ofColon_insertsNbspBefore`
- `kiyose_fr_nbsp_ofSemicolon_insertsNbspBefore`
- `kiyose_fr_nbsp_ofMultiplePunctuations_insertsAllNbsps`
- `kiyose_fr_nbsp_ofPunctuationWithoutSpace_doesNothing` — `'Charlie?'` reste inchangé (évite les faux positifs sur URL/acronyme)
- `kiyose_fr_nbsp_ofUnicodeInput_preservesCharacters` — caractères accentués (« être », « où »)

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/css/main.css` | Ajouter `text-wrap: balance` au bloc `h1..h6` |
| `latelierkiyose/assets/css/main.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/home-sections.css` | Ajouter `text-wrap: balance` à `.home-emphase` |
| `latelierkiyose/assets/css/components/home-sections.min.css` | Régénérer |
| `latelierkiyose/inc/content-filters.php` | Ajouter `kiyose_fr_nbsp()` |
| `latelierkiyose/templates/page-home.php` | Wrapper 6 sorties dynamiques (l. 89, 91, 119, 176, 185, 217) |
| `latelierkiyose/tests/Test_Content_Filters.php` | **Nouveau** — tests unitaires de `kiyose_fr_nbsp()` |

## Accessibilité

| Critère | Validation |
|---|---|
| Lecteurs d'écran | `&#8239;` (U+202F) traité comme espace — aucune altération de la prononciation |
| Contraste | Inchangé |
| Zoom 200% | Le rééquilibrage `balance` reste actif, pas de coupure brutale |
| Reflow (WCAG 1.4.10 AA) | `text-wrap: balance` respecte le viewport ; aucune largeur fixe introduite |
| Navigation clavier | Aucun changement structurel du DOM |

## Hors périmètre

- Application de `kiyose_fr_nbsp()` sur les paragraphes/body text (les retours de ponctuation y sont acceptables).
- Extension à d'autres templates (archives, single post, page contact, etc.) — à évaluer dans un PRD suivant si besoin.
- Gestion complète de la typographie française : guillemets « », tirets cadratins, apostrophes typographiques — potentiel PRD dédié.
- Polyfill pour navigateurs pré-2024 : non requis (dégradation gracieuse de `text-wrap: balance` vers `normal`).

## Tests et validation

### Fonctionnel
- [ ] Sur la home, un titre dynamique finissant par `?` ne laisse plus ce caractère seul sur une ligne (test à 320px, 768px, 1024px, 1440px)
- [ ] Les questions Q&A (`.home-content1__question`) ne présentent plus de `?` orphelin (traité uniquement par `kiyose_fr_nbsp()`)
- [ ] Les 3 slogans `.home-emphase` sont rééquilibrés visuellement et ne présentent plus de `?`, `!`, `:`, `;` isolés
- [ ] Les titres longs (h1 welcome) sont rééquilibrés visuellement (lignes de largeur comparable)

### Typographie
- [ ] L'inspecteur du navigateur montre bien `&#8239;` (U+202F) avant chaque `?`, `!`, `:`, `;` des 6 sorties dynamiques ciblées
- [ ] Aucune espace insécable n'est injectée dans les paragraphes body (`home-about__text`, `home-content1__answer`, etc.)
- [ ] Les h2 statiques sans ponctuation restent inchangés côté DOM (seul `text-wrap: balance` s'applique)

### Accessibilité
- [ ] Navigation clavier inchangée
- [ ] Lecture par lecteur d'écran (VoiceOver ou NVDA) : pas d'artefact sonore sur les espaces insécables

### Standards
- [ ] `make test` passe (PHPCS + Stylelint + ESLint)
- [ ] PHPUnit : les tests `Test_Content_Filters` sont verts
- [ ] Variables CSS préfixées `--kiyose-` (aucune nouvelle variable introduite ici)
