# PRD 0004 — Menu mobile

## Objectif

Implémenter le menu mobile (hamburger) avec une expérience accessible complète : focus trap, fermeture par Esc, overlay, verrouillage du scroll.

## Dépendances

- **PRD 0003** : Header et navigation en place.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
└── assets/
    └── js/
        └── modules/
            └── mobile-menu.js     # Logique du menu mobile
```

### Fichiers modifiés

- `header.php` — Ajouter le markup du menu mobile et de l'overlay
- `assets/css/components/navigation.css` — Ajouter les styles du menu mobile
- `inc/enqueue.php` — Enregistrer le script `mobile-menu.js`

## Spécifications détaillées

### Markup HTML (dans `header.php`)

```html
<!-- Bouton hamburger (visible uniquement en mobile < 1024px) -->
<button
    class="hamburger-button"
    aria-expanded="false"
    aria-controls="mobile-menu"
    aria-label="Ouvrir le menu de navigation"
>
    <span class="hamburger-button__line"></span>
    <span class="hamburger-button__line"></span>
    <span class="hamburger-button__line"></span>
</button>

<!-- Overlay -->
<div class="mobile-menu-overlay" aria-hidden="true"></div>

<!-- Menu mobile -->
<div
    id="mobile-menu"
    class="mobile-menu"
    aria-hidden="true"
    role="dialog"
    aria-label="Menu de navigation"
>
    <button
        class="mobile-menu__close"
        aria-label="Fermer le menu de navigation"
    >
        <!-- Icône fermeture (×) -->
    </button>

    <nav aria-label="Menu principal">
        <!-- Même contenu que wp_nav_menu principal,
             ou duplication avec wp_nav_menu 'primary' -->
    </nav>
</div>
```

### Comportement JS (`mobile-menu.js`)

**Module ES6** — Exporté comme module, initialisé depuis `main.js`.

**Ouverture du menu** :
1. Clic sur le bouton hamburger
2. `aria-expanded="true"` sur le bouton
3. `aria-hidden="false"` sur le menu et l'overlay
4. Classe CSS pour animer l'ouverture (slide-in depuis la droite)
5. `document.body.style.overflow = 'hidden'` (bloquer le scroll)
6. Focus placé sur le bouton de fermeture
7. Focus trap activé (Tab circule dans le menu uniquement)

**Fermeture du menu** :
1. Clic sur le bouton de fermeture, ou touche Esc, ou clic sur l'overlay
2. `aria-expanded="false"` sur le bouton hamburger
3. `aria-hidden="true"` sur le menu et l'overlay
4. `document.body.style.overflow = ''` (débloquer le scroll)
5. Focus retourné sur le bouton hamburger

**Focus trap** :
- Identifier tous les éléments focusables dans le menu
- Sur Tab du dernier élément → focus sur le premier
- Sur Shift+Tab du premier élément → focus sur le dernier

**Transition** :
- `transform: translateX(100%)` → `translateX(0)` pour l'ouverture
- Durée : 300ms
- Respecter `prefers-reduced-motion` : si activé, pas de transition (`transition: none`)

### Styles CSS

Reprendre les styles de `references/css-examples.css` (sections NAVIGATION) :
- `.mobile-menu` : position fixed, largeur 100% / max-width 320px, fond blanc
- `.mobile-menu-overlay` : position fixed, fond semi-transparent
- `.hamburger-button` : 44x44px, 3 lignes avec animation en croix
- Breakpoint : affiché uniquement en dessous de 1024px
- `.hamburger-button[aria-expanded="true"]` : animation des lignes en croix

### Items de menu mobile

- Chaque item occupe toute la largeur
- Hauteur minimum 44px (touch target)
- Espacement confortable entre items
- Page courante mise en évidence (classe `current-menu-item`)
- Sous-menus : accordéon ou affichés directement (pas de dropdown comme en desktop)

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur les fichiers PHP modifiés

### Tests manuels — Fonctionnel
- [ ] Bouton hamburger visible uniquement en mobile (<1024px)
- [ ] Clic sur hamburger ouvre le menu avec animation slide-in
- [ ] Overlay visible derrière le menu
- [ ] Clic sur overlay ferme le menu
- [ ] Clic sur bouton fermeture ferme le menu
- [ ] Le scroll du body est bloqué quand le menu est ouvert
- [ ] Les liens du menu fonctionnent et naviguent vers les bonnes pages

### Tests manuels — Accessibilité
- [ ] `aria-expanded` change de valeur à l'ouverture/fermeture
- [ ] `aria-hidden` change de valeur sur le menu et l'overlay
- [ ] Focus trap : Tab circule dans le menu uniquement
- [ ] Touche Esc ferme le menu
- [ ] Focus retourne sur le bouton hamburger à la fermeture
- [ ] `aria-label` appropriés sur les boutons
- [ ] Navigation clavier complète dans les items du menu

### Tests manuels — Responsive
- [ ] Menu hamburger caché en desktop (≥1024px)
- [ ] Menu hamburger visible et fonctionnel en mobile (<1024px)
- [ ] Transition désactivée si `prefers-reduced-motion: reduce`

## Hors périmètre

- Contenu du menu (items configurés dans l'admin WordPress, hérités du PRD 0003)
- Animations avancées ou éléments décoratifs
