# Changement de la présentation du hero

**Statut** : prêt à l'implémentation

tl;dr :
- Le bloc hero actuel reste valable mais sa présentation change.
- Un nouveau bloc « bienvenue » s'affiche à sa place.
- L'ancien hero s'affiche dynamiquement sous forme d'overlay « À propos » au premier scroll.

---

## Desktop

### Bloc Bienvenue

#### Source de contenu

Nouvelles meta fields dans la meta box existante de la page d'accueil (même approche que les champs hero actuels).

#### Structure

```
TITRE                          ← meta field texte, Dancing Script 4xl/5xl
Sous-titre                     ← meta field texte, Nunito xl
Texte descriptif               ← meta field textarea, Nunito base
[Mot-clef 1] [Mot-clef 2] ... ← meta field repeater (label + URL par entrée)
Slogan                         ← meta field texte, Dancing Script 2xl/3xl
```

#### Mots-clefs

Les mots-clefs sont des liens cliquables (chips/pills). Chaque entrée du repeater contient :
- un label (texte affiché)
- une URL (peut pointer vers une ancre sur la même page ou vers une autre page)

Le nombre d'entrées est variable et géré via le repeater dans la meta box.

#### Slogan

Typographie Dancing Script (police heading). Taille comparable à un titre (~2xl/3xl). Mis en valeur visuellement : couleur accent ou burgundy, espacement généreux.

---

### Overlay « À propos »

#### Déclenchement

L'overlay apparaît au premier scroll, dès que l'utilisateur·ice a scrollé d'au moins 30px depuis le haut de la page.

Si l'utilisateur·ice arrive sur la page déjà scrollé·e (retour arrière, ancre dans l'URL), l'overlay apparaît immédiatement sans animation.

Une fois fermé, l'overlay ne réapparaît pas pendant la session de navigation sur cette page (pas de réapparition au retour en haut de page).

#### Position et dimensions

- Position fixe (`position: fixed`), coin supérieur droit
- Largeur ~360px
- Peut masquer partiellement le contenu de la page
- Z-index : au-dessus du contenu principal, en dessous du header sticky (si applicable) et des modales

#### Contenu

Issu des meta fields hero existantes :
- Photo (taille réduite par rapport au hero actuel)
- Description courte
- Lien vers la page « À propos »

#### Fermeture

- Bouton fermer (×) visible et accessible (minimum 44×44px, contraste suffisant)
- Raccourci clavier : Échap ferme l'overlay

#### Animation d'apparition

L'overlay glisse depuis hors-écran à droite, décrit un arc vers le bas, puis remonte en position finale (effet de trajectoire courbe, comme un saut dans l'écran).

- Durée : 1.5s à 2s
- Easing : `cubic-bezier` personnalisé pour l'effet de trajectoire courbe
- `prefers-reduced-motion: reduce` : l'overlay apparaît directement en position finale, sans animation

#### Disparition automatique au scroll

Reportée à un prochain PRD.

---

## Mobile (< 768px)

Pas d'overlay flottant, pas d'animation courbe. Le contenu « À propos » s'affiche comme une section pleine largeur intégrée dans le flux de la page.

Ordre des sections :
1. Hero (bloc bienvenue)
2. Séparateur vague
3. À propos
4. Séparateur vague
5. Services
6. (suite inchangée)

---

## Accessibilité

### Overlay

- Bouton fermer avec `aria-label="Fermer le panneau À propos"`
- Focusable au clavier (Tab pour atteindre le bouton fermer, Échap pour fermer)
- **Pas de focus trap** : l'overlay n'est pas modal — le contenu derrière reste accessible au clavier
- **Gestion du focus à l'ouverture** : l'overlay ne vole pas le focus lors de son apparition
- **Gestion du focus à la fermeture** : quand l'overlay est fermé via Échap, le focus est remis sur l'élément qui avait le focus avant l'ouverture (ou sur un élément logique si ce contexte n'est plus disponible)
- `aria-live="polite"` sur le conteneur de l'overlay pour annoncer son apparition aux lecteurs d'écran

### Animations

- `prefers-reduced-motion: reduce` : aucune animation, l'overlay apparaît directement en position finale

### Bouton fermer

- Taille minimale : 44×44px
- Contraste : ratio ≥ 4.5:1 par rapport à son arrière-plan

### Bloc Bienvenue

- Hiérarchie des titres respectée (le titre du bloc bienvenue doit être le `h1` de la page)
- Les mots-clefs (chips/liens) ont un texte de lien explicite ou un `aria-label` si le contexte visuel ne suffit pas
