# Structure de la page d'accueil

**Statut : terminé**

## Objectif

Restructurer la page d'accueil en remplaçant la section "Mes activités" (grille de services) par deux sections de contenu éditorial (Q&A + texte/citation) et en ajoutant un CTA "Réserver votre appel découverte" vers la page contact.

## Structure implémentée

```
Section                  | Fond
─────────────────────────┼──────────────
1. Hero + CTA            | rose (#f5ece8)
   Mobile "À propos"     | rose
   ~~~~ vague rose → blanc ~~~~
2. Contenu 1 (Q&A)       | blanc (#ffffff)
   ~~~~ vague blanc → rose ~~~~
3. Contenu 2 (texte+citation+CTA) | rose
   ~~~~ vague rose → blanc ~~~~
4. Events                | blanc
   ~~~~ vague blanc → rose ~~~~
5. Témoignages           | rose
   ~~~~ vague rose → blanc ~~~~
6. Actualités            | blanc
   ~~~~ vague blanc → rose ~~~~
7. Newsletter            | rose (toujours visible)
```

L'alternance rose/blanc est stricte. Les séparateurs (vagues) transmettent la couleur source et cible.

## Décisions d'architecture

| Question | Décision |
|----------|----------|
| Approche d'édition | Meta fields (cohérent avec le Hero existant) |
| Nombre de Q&A | Variable (repeater JSON, pattern des mots-clefs) |
| CTA URL | Hardcodée via constante `KIYOSE_DISCOVERY_CALL_URL = '/contact/'` |
| Overlay "À propos" | Conservé tel quel (desktop + mobile) |
| Newsletter | Toujours visible (suppression du `display: none` desktop) |

## Meta fields ajoutés

Sur la page utilisant le template `page-home.php` :

| Meta field | Type | Sanitization |
|-----------|------|-------------|
| `kiyose_content1_qa` | JSON `[{question, answer}, ...]` | `kiyose_sanitize_qa_items()` |
| `kiyose_content1_slogan` | texte brut | `sanitize_text_field()` |
| `kiyose_content2_text` | HTML limité | `wp_kses_post()` |
| `kiyose_content2_slogan` | texte brut | `sanitize_text_field()` |

Tous ces champs peuvent être vides — les sections sont masquées si vides.

## Fichiers créés

- `assets/css/components/home-content.css` — styles `.home-content1` (Q&A) et `.home-content2` (texte/citation/CTA)

## Fichiers modifiés

- `functions.php` — constante `KIYOSE_DISCOVERY_CALL_URL`
- `inc/meta-boxes.php` — sanitizer `kiyose_sanitize_qa_items()`, sections admin Contenu 1/2, logique de sauvegarde, suppression meta box homepage display
- `inc/enqueue.php` — enqueue `kiyose-home-content`, suppression `kiyose-service-card`
- `inc/setup.php` — suppression `add_image_size('kiyose-service-card', ...)`
- `templates/page-home.php` — nouvelle structure : sections Contenu 1/2, CTA Hero, suppression Services, séparateurs simplifiés
- `assets/css/components/home-sections.css` — backgrounds alternés rose/blanc, suppression `.home-services*`, newsletter toujours visible
- `assets/css/components/welcome-block.css` — ajout `.welcome-block__cta`
- `assets/css/components/animations.css` — suppression sélecteurs `.service-card`
- `assets/css/components/home-animations.css` — suppression animations `.service-card`
- `assets/js/home-animations.js` — suppression bloc animation service cards

## Fichiers supprimés

- `template-parts/content-service.php`
- `assets/css/components/service-card.css`
- `assets/css/components/service-card.min.css`
- `bin/migrate-homepage-services.php`

## Détail des blocs

### Hero

```
h1. Titre
Contenu
Mots-clefs
Slogan
CTA « Réserver votre appel découverte »  →  /contact/
```

### Contenu 1 (Q&A)

```
Question ?
Réponse

Question ?
Réponse

...

Slogan conclusion  (optionnel)
```

Questions en Dancing Script (même taille que slogan). Nombre variable. Section masquée si liste vide.

### Contenu 2

```
Texte riche (gras, italique, listes, liens)

Citation / slogan  (optionnel)

CTA « Réserver votre appel découverte »  →  /contact/
```

Section masquée si texte et slogan tous les deux vides.

## Accessibilité

- Sections Contenu 1 et 2 ont des `<h2 class="sr-only">` pour maintenir la hiérarchie des headings
- CTA : `min-height: 44px` (cible tactile WCAG), focus visible avec `outline-offset`
- Contraste texte burgundy sur fond blanc ET rose validé (ratio > 4.5:1)
