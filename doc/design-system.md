# Design System

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
| ![#C9ABA7](https://img.shields.io/badge/-C9ABA7-C9ABA7?style=flat-square) | `#C9ABA7` | Couleur secondaire - Backgrounds sections | Taupe rosé |
| ![#E6A528](https://img.shields.io/badge/-E6A528-E6A528?style=flat-square) | `#E6A528` | Accent or/kintsugi - Éléments décoratifs | Or miel |
| ![#F4C975](https://img.shields.io/badge/-F4C975-F4C975?style=flat-square) | `#F4C975` | Accent doré clair - Dégradés, highlights | Jaune doré |
| ![#5D0505](https://img.shields.io/badge/-5D0505-5D0505?style=flat-square) | `#5D0505` | Texte fort, logo, titres importants | Bordeaux foncé |
| ![#EFE5E4](https://img.shields.io/badge/-EFE5E4-EFE5E4?style=flat-square) | `#EFE5E4` | Couleur de fond - Background principal | Beige clair |

> **Note** : L'image `references/palette.jpg` contient une inversion entre les cercles 4 et 5 (les codes hex affichés sous les cercles sont échangés par rapport aux couleurs visibles). Les codes ci-dessus reflètent les couleurs réelles.

**Variables CSS**:
```css
:root {
  /* Couleurs principales */
  --color-primary: #D7A4A4;          /* Rose poudré */
  --color-secondary: #C9ABA7;        /* Taupe rosé */
  --color-accent: #E6A528;           /* Or miel */
  --color-background: #EFE5E4;       /* Beige clair */
  --color-burgundy: #5D0505;         /* Bordeaux foncé (logo, titres) */

  /* Couleurs fonctionnelles */
  --color-text: #333333;             /* Texte principal */
  --color-text-light: #666666;       /* Texte secondaire */
  --color-border: #C9ABA7;           /* Bordures */

  /* Or Kintsugi */
  --color-gold: #E6A528;             /* Or principal */
  --color-gold-light: #F4C975;       /* Jaune doré clair */
  --color-gold-transparent: rgba(230, 165, 40, 0.3); /* Or transparent (overlays) */
}
```

**Exigences accessibilité**:
- ✅ Tous les textes doivent avoir contraste ≥ 4.5:1 sur leur fond
- ✅ Boutons et éléments interactifs ≥ 3:1
- ✅ États focus visibles avec contraste suffisant
- ⚠️ **CRITIQUE**: Valider tous les contrastes avec [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/) avant implémentation
- ⚠️ Si certaines couleurs ne passent pas les tests de contraste, prévoir des ajustements (assombrir/éclaircir)

**Actions de validation requises**:
1. Tester tous les contrastes texte/fond avec les couleurs officielles
2. Ajuster si nécessaire pour conformité WCAG 2.2 AA
3. Créer des variantes (hover, focus, disabled) pour les états interactifs

## Identité visuelle Kintsugi

**Philosophie de design:**
L'identité visuelle s'inspire du Kintsugi japonais - l'art de réparer les céramiques avec de la poudre d'or. Cette métaphore guide le design: les imperfections et fêlures laissent passer la lumière, transformant les blessures en beauté.

**Éléments visuels Kintsugi:**

**1. Lignes dorées subtiles**
- Séparateurs de sections inspirés des fissures réparées à l'or
- Couleur: Or miel (`#E6A528`) - couleur officielle de la palette
- Épaisseur: 1-2px, style: irrégulier/organique (pas de lignes droites parfaites)
- Utilisation: Entre les sections de la homepage, autour des cartes de services
- Technique: SVG paths pour tracés organiques, ou CSS avec border-image

```css
.section-divider {
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent 0%,
    var(--color-gold-transparent) 20%,
    var(--color-gold) 50%,
    var(--color-gold-transparent) 80%,
    transparent 100%
  );
  position: relative;
  margin: var(--spacing-3xl) 0;
}
```

**2. Effets vitrail (overlays colorés)**
- Overlays semi-transparents évoquant "l'être humain comme un vitrail"
- Utilisation: Backgrounds de sections avec dégradés de la palette (taupe rosé, rose poudré)
- Opacité: 0.05-0.15 pour ne pas nuire à la lisibilité
- Technique: Multiple background-images avec blend-modes ou pseudo-elements ::before/::after

```css
.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background:
    radial-gradient(ellipse at 20% 30%, rgba(201, 171, 167, 0.15) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 70%, rgba(215, 164, 164, 0.15) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}
```

**3. Imagerie Kintsugi**
- Graphisme subtil: motifs de fissures dorées en arrière-plan (opacity très faible)
- Icône: Possibilité d'utiliser un symbole Kintsugi stylisé comme élément graphique récurrent

**Contraintes accessibilité:**
- Tous les éléments dorés sont purement décoratifs (pas d'information transmise uniquement par la couleur)
- Contraste suffisant maintenu pour tous les textes (≥ 4.5:1)
- Overlays et backgrounds n'interfèrent jamais avec la lisibilité du contenu

## Typographie

**Approche:**
- **Titre (serif)**: Police élégante et chaleureuse (ex: Playfair Display, Lora, Crimson Text)
- **Corps (sans-serif)**: Police lisible et moderne (ex: Inter, Open Sans, Nunito)
- **Hébergement**: Auto-hébergées dans `assets/fonts/` (conformité RGPD — pas de chargement depuis Google Fonts CDN)
- Toutes les polices doivent être optimisées (subset latin, formats woff2 + woff, preload pour les polices critiques)
- Déclaration via `@font-face` dans `main.css`, chargement via `kiyose_enqueue_styles()` dans `inc/enqueue.php`

**Échelle typographique:**
```css
:root {
  --font-size-xs: 0.75rem;    /* 12px */
  --font-size-sm: 0.875rem;   /* 14px */
  --font-size-base: 1rem;     /* 16px - taille minimum pour body */
  --font-size-lg: 1.125rem;   /* 18px */
  --font-size-xl: 1.25rem;    /* 20px */
  --font-size-2xl: 1.5rem;    /* 24px */
  --font-size-3xl: 2rem;      /* 32px */
  --font-size-4xl: 2.5rem;    /* 40px */
}
```

**Line height:**
- Texte corps: 1.6-1.8 (confort de lecture)
- Titres: 1.2-1.4

## Espacement

**Système d'espacement cohérent:**
```css
:root {
  --spacing-xs: 0.25rem;   /* 4px */
  --spacing-sm: 0.5rem;    /* 8px */
  --spacing-md: 1rem;      /* 16px */
  --spacing-lg: 1.5rem;    /* 24px */
  --spacing-xl: 2rem;      /* 32px */
  --spacing-2xl: 3rem;     /* 48px */
  --spacing-3xl: 4rem;     /* 64px */
}
```

**Principe**: Espacement généreux pour sensation de calme et bien-être

## Composants UI principaux

**Boutons:**
- **Primary**: Fond rose poudré (#D7A4A4), texte bordeaux (#5D0505), border-radius arrondi
- **Secondary**: Outline rose poudré (#D7A4A4), fond transparent
- **States**: Hover (assombrir 10%), Focus (outline visible), Active, Disabled
- Taille minimum: 44x44px (touch target WCAG 2.2)

**Cartes (Services, Témoignages):**
- Fond blanc ou beige clair (#EFE5E4)
- Ombre légère (box-shadow subtile)
- Border-radius arrondi
- Padding généreux
- Effet hover subtil (élévation, scale léger)

**Formulaires:**
- Labels visibles au-dessus des champs (pas de placeholder seul)
- Bordures claires, focus visible
- Messages d'erreur en rouge accessible (≥ 4.5:1 contraste)
- Icônes de validation (✓ vert, ✗ rouge)

**Navigation dropdown:**
- Animation douce (transition 200-300ms)
- Flèche indicative pour items avec sous-menu
- Accessible clavier (Tab, Enter, Esc, flèches)
- Focus trap dans le dropdown ouvert

## Animations & Transitions

**Principes généraux:**
- Animations subtiles et rapides (200-300ms)
- Respecter `prefers-reduced-motion` (désactiver animations si activé)
- Pas d'animations automatiques qui distraient
- Transitions sur hover/focus pour feedback utilisateur

**Bulles décoratives interactives:**
- Pas d'animation automatique (conformité WCAG 2.2 AA)
- Bulles SVG apparaissent au survol/focus d'éléments interactifs
- Animation déclenchée uniquement par interaction utilisateur
- Timing: apparition fade-in 300ms, disparition fade-out 200ms
- Couleurs: palette officielle (rose poudré, taupe rosé) avec opacité 0.6-0.8
- Positionnement: ne jamais chevaucher du contenu important
- Accessible au clavier (déclenchement sur focus)

**Exemples d'implémentation:**
```css
/* Bulles désactivées par défaut */
.bubble {
  opacity: 0;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Apparition au survol de cartes de services */
.service-card:hover .bubble,
.service-card:focus-within .bubble {
  opacity: 0.7;
  transform: translateY(-5px);
}

/* Respecter prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  .bubble {
    transition: none;
    transform: none;
  }
}
```

**Autres transitions:**
```css
@media (prefers-reduced-motion: no-preference) {
  .button {
    transition: background-color 0.2s ease;
  }
  .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
}
```

## Images & Médias

**Formats:**
- WebP avec fallback JPG/PNG
- SVG pour icônes et logos
- Optimisation avant upload (TinyPNG, Squoosh)

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