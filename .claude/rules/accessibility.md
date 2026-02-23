---
description: WCAG 2.2 AA accessibility requirements — absolute priority
globs:
  - "latelierkiyose/**/*.php"
  - "latelierkiyose/**/*.css"
  - "latelierkiyose/**/*.js"
---

# Accessibilité WCAG 2.2 AA

L'accessibilité est la priorité absolue de ce projet.

## Contraste
- Texte : ratio ≥ 4.5:1
- Composants UI et graphiques : ratio ≥ 3:1
- Vérifier avec WebAIM Contrast Checker avant de choisir des couleurs

## Navigation clavier
- Tous les éléments interactifs doivent être atteignables au clavier (Tab)
- Focus visible obligatoire sur tous les éléments focusables
- Patterns Échap pour fermer les overlays/modales
- Skip link en début de page

## Structure
- Headings hiérarchiques (h1 → h2 → h3, pas de saut)
- Landmarks ARIA (banner, navigation, main, contentinfo)
- Images : attribut `alt` pertinent (ou `alt=""` si décoratif)

## Responsive & touch
- Largeur minimale : 320px
- Cibles tactiles : minimum 44x44px
- Tester sur mobile (iOS Safari, Chrome Android)
