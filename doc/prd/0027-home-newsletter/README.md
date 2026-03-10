# Inscription à la newsletter depuis la home

**Statut** : prêt à l'implémentation

**Dépendance** : PRD 0025 (overlay « À propos »). Ce PRD étend le comportement de l'overlay « À propos » en ajoutant la disparition automatique au scroll, initialement reportée dans le PRD 0025.

---

## Objectif

Proposer l'inscription à la newsletter (formulaire Brevo) de manière proactive sur la page d'accueil, via un overlay desktop déclenché au scroll et une section inline sur mobile.

Le contenu du formulaire d'inscription à la newsletter est le même sur desktop et mobile.

---

## Desktop

### Deux overlays, mutuellement exclusifs

La home gère deux overlays fixes qui ne s'affichent **jamais en même temps** :

| Overlay             | Position | Déclencheur d'apparition          | Déclencheur de disparition                      |
| ------------------- | -------- | --------------------------------- | ----------------------------------------------- |
| À propos (PRD 0025) | Droite   | Au-dessus de « Prochaines dates » | En descendant au niveau de « Prochaines dates » |
| Newsletter (ce PRD) | Gauche   | Au niveau de « Prochaines dates » | En remontant au-dessus de « Prochaines dates »  |

### Détection du seuil de scroll

Le seuil de transition est défini par la position de la section `.home-events` (« Prochaines dates ») dans le viewport. La bascule se fait quand le **bord supérieur** de `.home-events` atteint le bord supérieur du viewport (ou légèrement au-dessus, ~100px de marge pour éviter les oscillations).

Utiliser `IntersectionObserver` sur `.home-events` avec un `rootMargin` approprié, ou un scroll handler avec debounce.

### Overlay « À propos » — Modification du comportement

Le comportement initial (PRD 0025) est étendu :

- **Apparition** : inchangée (au premier scroll >= 30px)
- **Disparition au scroll** (nouveau) : l'overlay se masque quand l'utilisateur scrolle au niveau de « Prochaines dates »
- **Réapparition** : en remontant au-dessus de « Prochaines dates », l'overlay réapparaît — **sauf s'il a été fermé manuellement** (bouton × ou Échap)
- **Fermeture manuelle** : une fois fermé, ne réapparaît plus jusqu'au rechargement de la page (inchangé par rapport au PRD 0025)

### Overlay « Newsletter »

#### Position et dimensions

- `position: fixed`, côté **gauche** de l'écran
- Centré verticalement dans le viewport (environ au milieu)
- Largeur : ~360px (similaire à l'overlay « À propos »)
- Z-index : au même niveau que l'overlay « À propos » (au-dessus du contenu, en dessous du header sticky et des modales)

#### Contenu

- **Titre** : "Restez informé·e" (ou équivalent — `<h2>` visuellement stylé en tant que titre de panneau)
- **Description** : texte court d'accroche (ex. : « Recevez les prochaines dates et actualités directement dans votre boîte mail. »)
- **Formulaire** : shortcode Brevo `[sibwp_form id=1]` (le même que dans la section inline et le footer)

#### Apparition

- L'overlay apparaît quand l'utilisateur scrolle au niveau de « Prochaines dates »
- **Sauf s'il a été fermé manuellement** pendant cette session de page

#### Animation d'apparition

Animation en arc miroir de l'overlay « À propos », depuis la gauche :

- Glissement depuis hors-écran à gauche, arc vers le bas, puis remontée en position finale
- Durée : 700ms
- Easing : même `cubic-bezier` que l'overlay « À propos »
- `prefers-reduced-motion: reduce` : apparition directe en position finale, sans animation

#### Disparition

- L'overlay se masque quand l'utilisateur remonte au-dessus de « Prochaines dates »
- Disparition sans animation (masquage immédiat via `hidden`)

#### Fermeture manuelle

- Bouton fermer (×) visible et accessible (minimum 44x44px, contraste >= 4.5:1)
- Raccourci clavier : Échap ferme l'overlay
- **Après fermeture manuelle** : l'overlay peut réapparaître si l'utilisateur scrolle à nouveau au-delà du seuil (la fermeture manuelle n'est pas définitive ; elle ne supprime l'overlay que le temps de remonter et redescendre)

> **Note** : Ce comportement diffère de l'overlay « À propos » dont la fermeture manuelle est définitive pour la session. Ici, le but est de ne pas être intrusif tout en rappelant la possibilité de s'inscrire à chaque passage.

### Section newsletter inline — Desktop

La section inline `.home-newsletter` existante dans le flux de page est **masquée sur desktop** (`display: none` à partir de 768px). L'overlay la remplace sur desktop.

---

## Mobile (< 768px)

### Pas d'overlay

Aucun overlay newsletter sur mobile. Le formulaire est affiché dans une section inline intégrée dans le flux de la page.

### Déplacement de la section newsletter

La section newsletter existante (`.home-newsletter`) est **déplacée** dans l'ordre des sections pour se positionner **après « Prochaines dates »** :

**Nouvel ordre des sections (mobile) :**

1. Bienvenue (hero)
2. Séparateur vague
3. À propos (section mobile inline)
4. Séparateur vague
5. Services / 4 Piliers
6. Séparateur vague
7. Prochaines dates
8. Séparateur vague
9. **Newsletter** (déplacée ici)
10. Séparateur vague
11. Témoignages
12. Séparateur vague
13. Blog / Actualités

Le contenu de la section reste identique : titre « Restez informé·e », description, formulaire Brevo.

Les séparations entre sections restent cohérentes (séparateurs « vague » entre chaque section).

---

## Accessibilité

### Overlay newsletter

- `role="complementary"` (non modal, comme l'overlay « À propos »)
- `aria-label="Inscription à la newsletter"` sur le conteneur
- **Pas de focus trap** : le contenu derrière reste accessible au clavier
- **Pas de vol de focus** : l'overlay n'attrape pas le focus lors de son apparition
- **Focus à la fermeture** : quand l'overlay est fermé via Échap, le focus est restauré sur l'élément précédemment focusé (ou un élément logique si indisponible)
- Région `aria-live="polite"` pour annoncer l'apparition/disparition aux lecteurs d'écran

### Transitions d'overlays

- Les transitions entre overlays (À propos → Newsletter et inversement) sont annoncées via la région `aria-live="polite"` partagée
- Délai minimal entre les annonces pour éviter la saturation du lecteur d'écran (ex. : n'annoncer que l'overlay qui apparaît, pas celui qui disparaît)

### Bouton fermer

- Taille minimale : 44x44px (WCAG 2.2)
- Contraste : ratio >= 4.5:1 par rapport à son arrière-plan
- `aria-label="Fermer le panneau Newsletter"`

### Animations

- `prefers-reduced-motion: reduce` : aucune animation, overlays affichés/masqués immédiatement
- Les transitions de scroll (hide/show) ne sont pas des animations — elles utilisent `hidden`/`removeAttribute('hidden')`

### Formulaire Brevo

- Labels visibles sur tous les champs
- Messages d'erreur accessibles (contraste >= 4.5:1, associés via `aria-describedby`)
- Override CSS Brevo conforme aux standards d'accessibilité du thème (cf. `brevo-override.css`)

---

## Cas limites

### Scroll rapide

Si l'utilisateur scrolle rapidement à travers la zone de « Prochaines dates » (dans un sens ou l'autre), les overlays ne doivent pas clignoter. Utiliser un debounce ou un seuil d'hystérésis (ex. : ~100px de marge au-delà du seuil avant de déclencher la transition inverse).

### Les deux overlays fermés manuellement

Si l'utilisateur a fermé les deux overlays manuellement :

- L'overlay « À propos » ne réapparaît jamais (session)
- L'overlay « Newsletter » réapparaît au prochain passage par le seuil de scroll
- Aucun cas où rien ne s'affiche de manière permanente, sauf si l'utilisateur reste au-dessus de « Prochaines dates » après avoir fermé l'overlay « À propos »

### Redimensionnement (responsive)

- Si le viewport passe en dessous de 768px alors qu'un overlay est visible : masquer l'overlay, afficher la section inline mobile
- Si le viewport repasse au-dessus de 768px : reprendre le comportement desktop (scroll-triggered)

### Page chargée en position scrollée

Si la page est chargée avec un scroll déjà au-delà de « Prochaines dates » (ancre, retour arrière) :

- L'overlay « Newsletter » s'affiche immédiatement, sans animation
- L'overlay « À propos » ne s'affiche pas (il ne s'affiche que quand on est au-dessus de « Prochaines dates »)

---

## Implémentation (notes techniques)

### Fichiers concernés

| Fichier                                        | Modification                                                                                                                                                                  |
| ---------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `templates/page-home.php`                      | Déplacer la section newsletter dans l'ordre mobile ; ajouter le markup de l'overlay newsletter ; ajouter les classes CSS conditionnelles desktop/mobile sur la section inline |
| `assets/js/modules/about-overlay.js`           | Refactorer pour gérer la visibilité basée sur le scroll (IntersectionObserver ou scroll handler) au lieu du simple one-shot                                                   |
| `assets/js/modules/newsletter-overlay.js`      | Nouveau module JS pour l'overlay newsletter (même pattern que about-overlay)                                                                                                  |
| `assets/css/components/newsletter-overlay.css` | Styles de l'overlay newsletter (miroir de about-overlay, positionné à gauche)                                                                                                 |
| `assets/css/components/home-sections.css`      | Media query pour masquer `.home-newsletter` sur desktop (>= 768px)                                                                                                            |
| `inc/enqueue.php`                              | Enqueue des nouveaux CSS/JS sur la homepage                                                                                                                                   |

### Coordination JS des overlays

Les deux modules JS (about-overlay et newsletter-overlay) doivent partager un état de scroll ou communiquer via un mécanisme simple (ex. : `CustomEvent` sur `document`, ou un module coordinateur commun). Pas de couplage direct entre les deux modules.
