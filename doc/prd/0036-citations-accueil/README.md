# PRD 0036 — Emphase et citations sur l'accueil

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-04-06

## Contexte

La page d'accueil affiche trois slogans/citations mis en avant :

| Sélecteur CSS            | Section   | Fond actuel |
| ------------------------ | --------- | ----------- |
| `.welcome-block__slogan` | Bienvenue | `#EFE5E4`   |
| `.home-content1__slogan` | Contenu 1 | `#ffffff`   |
| `.home-content2__slogan` | Contenu 2 | `#EFE5E4`   |

Ces trois éléments utilisent actuellement Dancing Script (cursive heading), ce qui nuit à la lisibilité. Ils n'ont aucun traitement visuel particulier (pas de fond, pas de cadre) pour les distinguer du texte environnant.

## Objectif

1. **Améliorer la lisibilité** en passant à Nunito SemiBold (600), la police body du site.
2. **Créer une mise en valeur visuelle** avec des guillemets français décoratifs en or, cohérents avec l'identité Kintsugi du site.

## Décisions de conception

| Sujet           | Décision                                         | Justification                                             |
| --------------- | ------------------------------------------------ | --------------------------------------------------------- |
| Périmètre       | Les 3 slogans homepage                           | Uniformité visuelle                                       |
| Typographie     | Nunito SemiBold (600), taille `xl`               | Lisibilité maximale ; déjà chargée (400/600/700)          |
| Fond            | Aucun (fond de section conservé)                 | Décision post-implémentation : pas d'encadrement coloré   |
| Guillemets      | `« »` décoratifs en or `#E6A528`, Dancing Script | Rappel subtil de la cursive, touche d'élégance            |
| HTML            | Garder `<p>` (pas de `<blockquote>`)             | Ce sont des slogans, pas des citations au sens sémantique |
| Max-width       | 700px                                            | Confort de lecture, centrage                              |
| Classe partagée | `.home-emphase` ajoutée à chaque `<p>` slogan    | Centralisation des styles dans `home-sections.css`        |

## Spécifications CSS

### Composant `.home-emphase`

```css
/* ============================================
   HOME EMPHASE — Slogans mis en valeur
   ============================================ */

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
}

.home-emphase::before,
.home-emphase::after {
	color: #e6a528;
	font-family: var(--kiyose-font-heading);
	font-size: 1.4em;
	line-height: 1;
}

.home-emphase::before {
	content: '\00AB\00A0';
}

.home-emphase::after {
	content: '\00A0\00BB';
}
```

Pas de responsive spécifique — taille `xl` uniforme.

## Fichiers impactés

### PHP

| Fichier                            | Modification                                                   |
| ---------------------------------- | -------------------------------------------------------------- |
| `templates/page-home.php` (l. 119) | Ajouter `home-emphase` à la classe de `.welcome-block__slogan` |
| `templates/page-home.php` (l. 185) | Ajouter `home-emphase` à la classe de `.home-content1__slogan` |
| `templates/page-home.php` (l. 217) | Ajouter `home-emphase` à la classe de `.home-content2__slogan` |

### CSS

| Fichier                                   | Modification                                                                                                                                                                                                            |
| ----------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `assets/css/components/home-sections.css` | Ajouter le bloc `.home-emphase` (styles + responsive)                                                                                                                                                                   |
| `assets/css/components/welcome-block.css` | Supprimer `color`, `font-family`, `font-size`, `line-height` de `.welcome-block__slogan` (garder `margin`) ; supprimer le media query `font-size` tablette                                                              |
| `assets/css/components/home-content.css`  | Supprimer `color`, `font-family`, `font-size`, `font-weight`, `text-align` de `.home-content1__slogan` et `.home-content2__slogan` (garder `margin-*`) ; supprimer les media queries `font-size` tablette pour les deux |

Les fichiers `.min.css` correspondants doivent être regénérés.

## Accessibilité

| Critère                                                       | Conformité                                                                                                                                           |
| ------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- |
| Contraste texte (burgundy `#5D0505` sur cream gold `#FDF0D5`) | 12.49:1 — AA ✅ (vérifié PRD 0028)                                                                                                                   |
| Guillemets décoratifs (or `#E6A528` sur cream gold `#FDF0D5`) | Pseudo-éléments CSS = décoratifs, exemptés WCAG 1.4.3                                                                                                |
| Navigation clavier                                            | Aucun élément interactif ajouté — pas d'impact                                                                                                       |
| Lecteurs d'écran                                              | Les pseudo-éléments `::before`/`::after` avec `content` textuel seront lus ; les guillemets français sont une ponctuation standard, pas de confusion |
| `prefers-reduced-motion`                                      | Aucune animation ajoutée                                                                                                                             |

## Critères de validation

- [ ] Les 3 slogans s'affichent en Nunito SemiBold 600
- [ ] Fond cream gold visible sur les 3 slogans
- [ ] Guillemets `« »` en or affichés avant/après chaque slogan
- [ ] Max-width 700px respecté, centrage horizontal
- [ ] Responsive : `font-size` 2xl (mobile) → 3xl (≥ 768px)
- [ ] Contraste WCAG 2.2 AA validé
- [ ] `make test` passe (PHPCS, Stylelint, ESLint)
- [ ] Aucune régression visuelle sur les sections adjacentes
