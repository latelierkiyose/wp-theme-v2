# Design system

## Logo

**Fichier de référence**: `references/logo-kiyose.png`

**Caractéristiques du logo actuel (à conserver)**:
- Typographie script calligraphique élégante et chaleureuse
- Composition: "l'atelier" (minuscules) + "Kiyose" (capitale initiale)
- Couleur principale: Bordeaux foncé (`#5D0505` — couleur officielle de la palette)
- Éléments décoratifs: Courbes dorées organiques, points colorés
- Style: Chaleureux, créatif, artisanal, évoquant la créativité et l'authenticité

**Utilisation dans le thème**:
- Logo dans le header (version complète)
- Favicon (version simplifiée si nécessaire)
- Format: SVG optimisé pour le web (vectoriel + léger)
- Fallback PNG pour compatibilité
- Attribut `alt="L'atelier Kiyose"` pour accessibilité

## Palette de couleurs

**Fichier de référence**: `references/palette.jpg`

**Palette officielle**:

| Couleur | Code Hex | Usage prévu | Nom |
|---------|----------|-------------|-----|
| ![#D7A4A4](https://img.shields.io/badge/-D7A4A4-D7A4A4?style=flat-square) | `#D7A4A4` | Couleur primaire - Accents, boutons, liens | Rose poudré |
| ![#C9ABA7](https://img.shields.io/badge/-C9ABA7-C9ABA7?style=flat-square) | `#C9ABA7` | Couleur secondaire - Arrière-plans de sections | Taupe rosé |
| ![#E6A528](https://img.shields.io/badge/-E6A528-E6A528?style=flat-square) | `#E6A528` | Accent or/kintsugi - Éléments décoratifs | Or miel |
| ![#F4C975](https://img.shields.io/badge/-F4C975-F4C975?style=flat-square) | `#F4C975` | Accent doré clair - Dégradés, highlights | Jaune doré |
| ![#5D0505](https://img.shields.io/badge/-5D0505-5D0505?style=flat-square) | `#5D0505` | Texte fort, logo, titres importants | Bordeaux foncé |
| ![#EFE5E4](https://img.shields.io/badge/-EFE5E4-EFE5E4?style=flat-square) | `#EFE5E4` | Couleur de fond - Arrière-plan principal | Beige clair |

> **Note** : L'image `references/palette.jpg` contient une inversion entre les cercles 4 et 5 (les codes hex affichés sous les cercles sont échangés par rapport aux couleurs visibles). Les codes ci-dessus reflètent les couleurs réelles.

**Variables CSS**: Voir `references/css-examples.css` pour l'implémentation complète des variables CSS.

**Exigences accessibilité**:
- ✅ Tous les textes doivent avoir contraste ≥ 4.5:1 sur leur fond
- ✅ Boutons et éléments interactifs ≥ 3:1
- ✅ États focus visibles avec contraste suffisant
- ⚠️ **CRITIQUE**: Valider tous les contrastes avec [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/) avant implémentation
- ⚠️ Si certaines couleurs ne passent pas les tests de contraste, prévoir des ajustements (assombrir/éclaircir)

**Actions de validation requises**:
1. Tester tous les contrastes texte/fond avec les couleurs officielles
2. Ajuster si nécessaire pour conformité WCAG 2.2 AA
3. Créer des variantes (survol, focus, désactivé) pour les états interactifs

**Ajustements de contraste autorisés**:
Les couleurs de la palette peuvent être légèrement assombries ou éclaircies pour atteindre les ratios WCAG AA, tout en restant dans l'esprit de la palette. Combinaisons connues à ajuster :
- Texte `#5D0505` sur bouton `#D7A4A4` : ratio 3.8:1 (insuffisant pour texte normal ≤18pt) — assombrir le fond ou éclaircir le texte
- Rose poudré `#D7A4A4` sur beige `#EFE5E4` : ratio 1.48:1 — ne jamais utiliser cette combinaison pour du texte
- Les variantes ajustées doivent être documentées comme variables CSS dédiées (ex: `--color-primary-dark`)

## Identité visuelle Kintsugi

**Philosophie de design:**
L'identité visuelle s'inspire du Kintsugi japonais - l'art de réparer les céramiques avec de la poudre d'or. Cette métaphore guide le design: les imperfections et fêlures laissent passer la lumière, transformant les blessures en beauté.

**Éléments visuels Kintsugi:**

**1. Lignes dorées subtiles**
- Séparateurs de sections inspirés des fissures réparées à l'or
- Couleur: Or miel (`#E6A528`)
- Épaisseur: 1-2px, style: irrégulier/organique
- Utilisation: Entre les sections de la homepage, autour des cartes de services
- **Implémentation**: Voir `references/css-examples.css#section-divider`

**2. Effets vitrail (overlays colorés)**
- Overlays semi-transparents évoquant "l'être humain comme un vitrail"
- Utilisation: Arrière-plans de sections avec dégradés (taupe rosé, rose poudré)
- Opacité: 0.05-0.15 pour ne pas nuire à la lisibilité
- **Implémentation**: Voir `references/css-examples.css#overlays-vitrail`

**3. Imagerie Kintsugi**
- Graphisme subtil: motifs de fissures dorées en arrière-plan (opacity très faible)
- Icône: Possibilité d'utiliser un symbole Kintsugi stylisé comme élément graphique récurrent

**Contraintes accessibilité:**
- Tous les éléments dorés sont purement décoratifs (pas d'information transmise uniquement par la couleur)
- Contraste suffisant maintenu pour tous les textes (≥ 4.5:1)
- Overlays et arrière-plans n'interfèrent jamais avec la lisibilité du contenu

## Typographie

**Polices retenues:**
- **Titres (manuscrite)**: **Caveat** — Manuscrite naturelle et lisible, playful mais professionnelle
- **Menu (sans-serif)**: **MK Abel** — Épurée et moderne (tall & thin), minimaliste
- **Corps (sans-serif)**: **Nunito** — Arrondie, très lisible, ton doux et accessible

**Hébergement**: Auto-hébergées dans `assets/fonts/` (conformité RGPD — pas de chargement depuis Google Fonts CDN)

**Optimisation:**
- Subset latin uniquement
- Formats: woff2 (principal) + woff (fallback)
- Preload pour les polices critiques (Caveat Regular, MK Abel Regular, Nunito Regular)
- Déclaration via `@font-face` dans `assets/css/fonts.css`, chargement via `kiyose_enqueue_styles()` dans `inc/enqueue.php`

**Graisses à inclure:**
- Caveat: Regular (400), Bold (700)
- MK Abel: Regular (400) uniquement
- Nunito: Regular (400), SemiBold (600), Bold (700)

**Échelle typographique**: Voir `references/css-examples.css` pour les variables CSS complètes.

**Hauteur de ligne (line-height):**
- Texte corps: 1.6-1.8 (confort de lecture)
- Titres: 1.2-1.4

## Espacement

**Système d'espacement cohérent**: Voir `references/css-examples.css` pour les variables CSS complètes.

**Principe**: Espacement généreux pour sensation de calme et bien-être

## Layout

**Largeur maximale du contenu:**

Le thème utilise une variable CSS centralisée pour la largeur maximale des containers :

```css
--container-max-width: 1600px;
```

**Utilisation:**
- Header (`.site-header__container`)
- Footer (`.site-footer__container`)
- Contenu principal (`.container`)
- Articles de type page (`article.page`)

**Pour modifier la largeur globale** :
Éditez uniquement `assets/css/variables.css` ligne ~73 :
```css
--container-max-width: 1600px; /* Ajustez cette valeur */
```

Cette modification s'appliquera automatiquement à tous les éléments utilisant cette variable.

**Breakpoints** (définis en commentaire dans `variables.css`, CSS ne supportant pas les variables dans les media queries) :
- Mobile : 320px - 767px
- Tablet : 768px+
- Desktop : 1024px+
- Desktop large : 1280px+

## Composants UI principaux

**Voir `references/css-examples.css` pour les implémentations CSS complètes des composants suivants:**

**Boutons:**
- Primaire: Fond rose poudré (#D7A4A4), texte bordeaux (#5D0505)
- Secondaire: Contour rose poudré, fond transparent
- États: Survol, Focus, Actif, Désactivé
- Taille minimum: 44x44px (WCAG 2.2)

**Cartes (Services, Témoignages):**
- Fond blanc ou beige clair (#EFE5E4)
- Ombre légère, coins arrondis (border-radius)
- Espacement intérieur (padding) généreux
- Effet au survol subtil

**Formulaires:**
- Labels visibles au-dessus des champs
- Bordures claires, focus visible
- Messages d'erreur accessibles (≥ 4.5:1 contraste)
- Icônes de validation

**Navigation déroulante (dropdown):**
- Animation douce (200-300ms)
- Accessible au clavier (Tab, Entrée, Échap, flèches)
- Piège de focus (focus trap) dans le menu déroulant ouvert

**Carousel témoignages:**
- Composant accessible conforme WCAG 2.2 AA
- Structure: `role="region"` avec `aria-roledescription="carousel"` et `aria-label`
- Slides: `role="group"` avec `aria-roledescription="slide"` et `aria-label="X sur Y"`
- Contrôles: boutons précédent/suivant + bouton pause/play
- Navigation clavier: flèches gauche/droite pour changer de slide
- `aria-live="polite"` sur le conteneur de slides (passe à `"off"` en mode autoplay)
- Autoplay: désactivé par défaut si `prefers-reduced-motion: reduce`
- Module JS dédié: `assets/js/modules/carousel.js`

## Animations & Transitions

**Principes généraux:**
- Animations subtiles et rapides (200-300ms)
- Respecter `prefers-reduced-motion` (désactiver animations si activé)
- Pas d'animations automatiques qui distraient
- Transitions au survol/focus pour retour visuel utilisateur

**Bulles décoratives interactives:**
- Pas d'animation automatique (conformité WCAG 2.2 AA)
- Bulles SVG apparaissent au survol/focus d'éléments interactifs
- Timing: apparition fade-in 300ms, disparition fade-out 200ms
- Couleurs: palette officielle (rose poudré, taupe rosé) avec opacité 0.6-0.8
- Positionnement: ne jamais chevaucher du contenu important

**Implémentation complète**: Voir `references/css-examples.css#bulles-decoratives` et `#animations-transitions`

## Images & Médias

**Formats:**
- WebP avec repli (fallback) JPG/PNG
- SVG pour icônes et logos
- Optimisation avant téléchargement (TinyPNG, Squoosh)

**Tailles WordPress custom** (dans `inc/setup.php`):
- Déclarer les tailles nécessaires via `add_image_size()` (ex: hero, service-card, testimony-thumb)
- Supprimer les tailles par défaut inutiles de WordPress pour réduire l'espace disque
- Utiliser `wp_get_attachment_image()` avec les tailles custom pour le rendu

**Responsive images:**
```html
<img
  src="image-800w.webp"
  srcset="image-400w.webp 400w, image-800w.webp 800w, image-1200w.webp 1200w"
  sizes="(max-width: 768px) 100vw, 50vw"
  alt="Description accessible"
  loading="lazy"
>
```

## Iconographie

- Utiliser des icônes simples et reconnaissables
- Cohérence de style (outline vs filled)
- Toujours accompagnées de labels textuels (ou `aria-label`)
- Couleur respectant le contraste minimum