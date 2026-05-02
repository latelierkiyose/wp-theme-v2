# PRD 0058 — Navigation principale responsive et sémantique

- **Statut** : prêt à l'implémentation
- **Criticité** : moyenne
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

La navigation principale répond globalement au besoin : menu desktop, sous-menu `Mes ateliers`, menu mobile avec hamburger, focus trap et fermeture Échap.

Deux défauts restent visibles avant production :

- à `1024px`, la navigation desktop se compresse et les libellés se cassent maladroitement sur plusieurs lignes ;
- le lien parent `Mes ateliers` est rendu comme un `<a href="/art-therapie/">`, mais le JavaScript empêche sa navigation au clic desktop pour l'utiliser comme bouton de dropdown.

La documentation projet indique pourtant que tous les items de navigation doivent être cliquables. Le comportement actuel mélange lien et bouton, ce qui crée une ambiguïté d'interaction.

## Problème à résoudre

Un lien qui ne navigue pas au clic est un mauvais contrat utilisateur et accessibilité. À certains breakpoints, le menu desktop devient aussi visuellement instable : le logo, l'espacement et la longueur des libellés entrent en concurrence.

Conséquences :

- la cible `Mes ateliers` ne fait pas ce que son rôle annonce ;
- l'ordre clavier reste utilisable, mais le comportement Enter/clic est surprenant ;
- le rendu à `1024px` donne une impression de finition moyenne ;
- la bascule mobile/desktop arrive trop tôt ou avec trop peu d'espace.

## Objectif

Rendre la navigation conforme au contrat attendu :

1. un lien de menu navigue quand il est activé ;
2. le sous-menu reste accessible au clavier et à la souris ;
3. le menu desktop ne se casse pas à `1024px` ;
4. le menu mobile reste disponible jusqu'au breakpoint où le menu desktop a réellement assez de place ;
5. les états ARIA reflètent l'ouverture réelle des sous-menus.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Parent `Mes ateliers` | Garder le lien cliquable | La spec dit que tous les items sont cliquables |
| Ouverture sous-menu | Hover/focus-within + clavier `ArrowDown` | Permet l'accès au sous-menu sans détourner le clic |
| Touche Enter | Laisser naviguer le lien | Conforme au rôle `<a>` |
| Touche Espace | Peut ouvrir le sous-menu sans navigation | Espace n'est pas l'activation principale d'un lien |
| Breakpoint desktop | Relever ou ajuster le breakpoint si `1024px` ne suffit pas | Mieux vaut un menu mobile propre qu'un desktop compressé |
| Logo | Réduire son emprise aux largeurs intermédiaires si nécessaire | Le logo ne doit pas forcer la navigation à se casser |

## Spécifications

### 1. Corriger le comportement du dropdown desktop

Modifier `latelierkiyose/assets/js/modules/dropdown.js`.

Comportement attendu :

- supprimer le `preventDefault()` sur le clic du lien parent ;
- `Enter` sur `Mes ateliers` suit le lien ;
- `ArrowDown` ouvre le sous-menu et focus le premier lien enfant ;
- `Escape` ferme le sous-menu et rend le focus au parent ;
- `focusout` ferme le sous-menu quand le focus quitte l'item ;
- `aria-expanded` passe à `true` quand le sous-menu est ouvert par focus/hover/clavier, puis `false` à la fermeture.

Ne pas transformer le lien parent en bouton sauf décision explicite de refaire le markup avec un bouton séparé.

### 2. Stabiliser le breakpoint desktop

Modifier `latelierkiyose/assets/css/components/header.css` et `navigation.css`.

Objectif :

- à `1024px`, utiliser encore le menu mobile si le menu desktop ne tient pas proprement ;
- ou réduire l'emprise du logo, des gaps et des paddings pour obtenir un desktop lisible.

Décision recommandée :

- conserver le menu mobile jusqu'à `1100px` ou `1180px` ;
- garder le desktop à partir de la largeur où tous les items tiennent sans retour de ligne excessif.

Le choix exact doit être validé visuellement sur :

- `1024px` ;
- `1100px` ;
- `1280px` ;
- `1440px`.

### 3. Préserver le menu mobile

Le menu mobile existant doit rester conforme :

- bouton hamburger `44x44px` ;
- `aria-expanded` mis à jour ;
- `aria-controls="mobile-menu"` ;
- focus trap actif ;
- fermeture via Échap, close, overlay ;
- retour focus au hamburger.

Ne pas modifier `mobile-menu.js` sauf si le changement de breakpoint expose une régression.

### 4. Ajouter des tests JavaScript

Créer ou compléter un test Node pour `dropdown.js` si l'infrastructure le permet.

Cas minimum :

- `Dropdown_whenParentLinkIsClicked_doesNotPreventNavigation`
- `Dropdown_whenArrowDownIsPressed_opensSubmenuAndFocusesFirstChild`
- `Dropdown_whenEscapeIsPressed_closesSubmenu`

Si le projet ne possède pas encore de tests DOM JS, ajouter au minimum un test de build/contrat qui lit le module et échoue si le handler de clic appelle `preventDefault()` sur les liens parent desktop.

### 5. Ajouter un test CSS de breakpoint

Ajouter un test CSS source qui vérifie :

- le breakpoint mobile/desktop est cohérent entre `header.css` et `navigation.css` ;
- les règles `main-nav` et `.hamburger-button` ne basculent pas à deux largeurs différentes ;
- les minifiés sont régénérés.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/js/modules/dropdown.js` | Rendre le parent cliquable et garder le sous-menu accessible |
| `latelierkiyose/assets/js/modules/dropdown.min.js` | Régénérer |
| `latelierkiyose/assets/js/modules/dropdown.min.js.map` | Régénérer |
| `latelierkiyose/assets/css/components/header.css` | Ajuster le breakpoint ou l'emprise logo/nav |
| `latelierkiyose/assets/css/components/navigation.css` | Aligner le breakpoint et les règles menu |
| `latelierkiyose/assets/css/components/header.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/navigation.min.css` | Régénérer |
| `tests/build-check.test.js` ou nouveau test JS | Ajouter le contrat JS/CSS si aucune suite DOM n'existe |

## Accessibilité

- WCAG 2.2 — `2.1.1 Keyboard` : les sous-menus doivent rester accessibles sans souris.
- WCAG 2.2 — `2.4.3 Focus Order` : l'ordre du menu doit rester logique.
- WCAG 2.2 — `2.4.7 Focus Visible` : focus visible sur parent et liens enfants.
- WCAG 2.2 — `4.1.2 Name, Role, Value` : ne pas annoncer un lien comme un bouton implicite.

## Documentation

Aucune documentation utilisateur n'est requise si les libellés et destinations du menu ne changent pas.

Aucune documentation release n'est requise sauf si le changement impose une configuration de menu différente dans WordPress.

## Hors périmètre

- Repenser toute l'architecture de navigation.
- Changer les libellés ou URLs de menu.
- Ajouter un troisième niveau de sous-menu.
- Modifier le contenu des pages services.
- Refaire le header visuellement au-delà du breakpoint/logo nécessaire.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| Le parent devient cliquable mais le sous-menu est moins découvrable | Garder l'indicateur visuel et l'ouverture au hover/focus |
| Breakpoint trop haut masque inutilement le menu desktop | Valider à 1100, 1180, 1280 et choisir la première largeur propre |
| Régression du menu mobile | Tester ouverture, fermeture, focus trap et retour focus |
| ARIA `expanded` ne suit pas le hover | Ajouter des handlers `mouseenter` / `mouseleave` si nécessaire, sans bloquer la navigation |

## Tests et validation

### Validation automatisée

- [ ] Test JS : clic sur le parent ne fait pas `preventDefault()`.
- [ ] Test JS : `ArrowDown` ouvre le sous-menu.
- [ ] Test JS : `Escape` ferme le sous-menu.
- [ ] Test CSS : breakpoints header/navigation alignés.
- [ ] `npm run build:js` et `npm run build:css` exécutés si sources modifiées.
- [ ] `npm run build:check` passe.
- [ ] `make test` passe.

### Validation manuelle

- [ ] Desktop large : cliquer `Mes ateliers` navigue vers sa page.
- [ ] Desktop large : survol/focus expose le sous-menu.
- [ ] Clavier : Tab puis ArrowDown permet d'entrer dans le sous-menu.
- [ ] Clavier : Escape ferme le sous-menu.
- [ ] `1024px` : navigation lisible, pas de menu desktop compressé.
- [ ] `390px` : menu mobile intact, focus trap et fermeture fonctionnent.
