# PRD 0020 — Éléments décoratifs "Art expressif"

## Statut

**prêt à l'implémentation**

## Objectif

Remplacer les éléments décoratifs actuels (bulles SVG circulaires, overlays vitrail) par un système visuel audacieux et joyeux — éclaboussures de peinture, gribouillis au marqueur épais, vagues organiques — qui exprime l'énergie brute et l'imperfection assumée de L'Atelier Kiyose.

## Contexte

Le site possède actuellement trois types d'éléments décoratifs Kintsugi :
- **Section dividers dorés** : Lignes dorées entre les sections (conservés et renforcés)
- **Overlays vitrail** : Gradients semi-transparents sur le hero et certaines sections
- **Bulles SVG** : Cercles roses sur les cartes services, visibles uniquement au hover

**Problème** : L'ensemble est trop sage, trop propre, trop géométrique, trop pastel. Le site doit mieux refléter le côté un peu fou, imparfait et lumineux de L'Atelier Kiyose.

**Direction** : Un langage visuel centré sur l'éclaboussure de peinture — le geste spontané, l'accident heureux, l'énergie brute — combiné à des gribouillis de marqueur épais et des vagues organiques. L'ensemble est lumineux (dominante or/jaune), imparfait (rien d'aligné, rien de symétrique), et vivant (mouvement, apparitions dynamiques).

## Images de référence

Trois visuels Canva de L'Atelier Kiyose servent de guide pour l'esprit et la palette :
- Flyer "Journal Créatif" : Formes abstraites bordeaux, blobs roses, fond crème
- Couverture "Cultiver ta joie de vivre" : Spirale bordeaux, gribouillis dorés, vague rose, virgules
- Page "Bonjour, je suis Virginie" : Même vocabulaire, mêmes couleurs

Un quatrième visuel de référence (`tache.png`) définit le style des éclaboussures de peinture : forme centrale irrégulière avec des pointes en étoile qui rayonnent, petites gouttelettes satellites projetées autour, et parfois une coulure qui dégouline.

**Vocabulaire visuel** :

| Élément | Description | Référence |
|---------|-------------|-----------|
| **Éclaboussure de peinture** | Tache avec pointes en étoile, gouttelettes satellites, éventuellement une coulure | `tache.png` — élément visuel central |
| Spirale épaisse | Tourbillon en trait de marqueur bordeaux, 4-8px d'épaisseur | Couverture, coin haut-gauche |
| Vague organique | Grande forme blob, bords fluides, haute opacité | Couverture, bas de page (rose) |
| Virgules / touches de pinceau | Coups de pinceau plat, petites formes allongées | Les deux, petites formes bordeaux foncé |
| Gribouillis libre | Squiggles au marqueur épais, décontractées | Couverture, coin bas-droit (doré) |

## Dépendances

- **PRD 0002** : Design tokens (variables CSS)
- **PRD 0008** : Page d'accueil (structure HTML des sections)
- **PRD 0014** : Kintsugi et finitions visuelles (section dividers dorés conservés)
- **PRD 0018** : Animations de la page d'accueil (pattern Intersection Observer réutilisé)

## Palette révisée

### Changement majeur : le doré/jaune devient dominant

Les éléments décoratifs sont dominés par l'or/jaune et le rose à parts égales, avec le bordeaux en accent de contraste. La palette existante est conservée mais les proportions changent radicalement : 40% or/jaune, 40% rose, 20% bordeaux (uniquement pour renforcer un contraste, pas comme couleur décorative principale).

### Nouvelles couleurs

| Couleur | Variable CSS | Hex | Usage |
|---------|-------------|-----|-------|
| **Crème doré** | `--kiyose-color-cream-gold` | `#FDF0D5` | Fond de sections lumineuses (alternative au beige) |
| **Jaune chaud** | `--kiyose-color-warm-yellow` | `#FBBE2E` | Taches de peinture, éclaboussures |

> Les couleurs Corail (`#E07A5F`) et Turquoise (`#7EB8B0`) prévues dans la version précédente du PRD sont abandonnées — elles ne correspondent pas à l'esprit des images de référence.

### Contrastes vérifiés (WCAG 2.2 AA)

| Combinaison | Ratio | Verdict |
|------------|-------|---------|
| Bordeaux `#5D0505` sur Crème doré `#FDF0D5` | ~12:1 | ✅ Excellent |
| Bordeaux `#5D0505` sur Jaune doré `#F4C975` | ~9:1 | ✅ Excellent |
| Bordeaux `#5D0505` sur Jaune chaud `#FBBE2E` | ~7:1 | ✅ Bon |
| Bordeaux `#5D0505` sur Or miel `#E6A528` | ~6.5:1 | ✅ Bon |
| Or miel `#E6A528` sur Crème doré `#FDF0D5` | ~1.7:1 | ❌ Décoratif uniquement |

> **Attention** : Les éléments décoratifs dorés (lignes Kintsugi, gribouillis) sont peu visibles sur fond jaune. Sur les sections à fond crème doré, les éléments décoratifs doivent utiliser le bordeaux ou le rose au lieu du doré.

### Sections à fond lumineux

Certaines sections alternent un fond **crème doré** (`#FDF0D5`) au lieu du beige clair (`#EFE5E4`) pour apporter de la luminosité et de la chaleur. Sections candidates :
- Hero (option : fond crème doré au lieu de beige)
- CTA / appel à l'action
- Sections "pair" dans l'alternance

Le choix entre fond crème classique et fond crème doré sera affiné lors de l'implémentation selon le rendu visuel.

## Concept : système décoratif en 3 couches

### Vue d'ensemble

1. **Formes organiques audacieuses** (couche fond) — Grosses vagues, blobs et éclaboussures de peinture. Haute opacité (30-60%). Couleurs franches. Rien de subtil.
2. **Cicatrices dorées Kintsugi** (couche intermédiaire) — Lignes dorées organiques et irrégulières (comme la ligne dorée du logo), utilisées avec parcimonie : filigrane sous la navigation, autour du logo, autour de certains blobs contenant du texte.
3. **Gribouillis, taches & virgules** (couche détail dynamique) — Spirales, squiggles, touches de pinceau, éclaboussures. Apparaissent au scroll avec du mouvement.

### Métaphore révisée

L'art-thérapie en action : un atelier joyeux où la peinture éclabousse, où le marqueur griffonne librement, où rien n'est propre et tout est vivant. Les éclaboussures sont les accidents heureux — la peinture qu'on lance et qui retombe en étoile. Les cicatrices dorées sont les traces de Kintsugi — la beauté dans les fissures, utilisées avec parcimonie comme un accent précieux. Les gribouillis sont les annotations spontanées, les petits gestes de joie qui remplissent les marges.

## Spécifications

### 1. Formes organiques audacieuses (couche fond)

**Principe** : De grandes formes SVG — éclaboussures de peinture, vagues organiques, blobs. Rien de géométrique, rien de symétrique. Haute opacité pour un impact visuel fort. Les éclaboussures sont l'élément signature : elles apportent le côté brut, spontané, "un peu fou".

**Technique** : SVG inline avec `<path>`. Deux styles de bords selon le type de forme :
- **Éclaboussures** : Bords en étoile irrégulière — pointes aiguës qui rayonnent depuis le centre, petits `<path>` satellites pour les gouttelettes projetées, éventuellement un `<path>` allongé pour une coulure
- **Vagues / blobs** : Courbes de Bézier fluides, organiques

Fill uni. Les formes sont grandes et débordent volontairement des sections.

**Caractéristiques visuelles** :
- **Fill** : Couleur unie, opacité 30-60% (beaucoup plus que le PRD précédent)
- **Taille** : Grandes, 200-600px. Certaines débordent de leur section
- **Rotation** : Inclinaisons marquées (-8° à +8°)
- **Pas d'ombre, pas de bordure** : Formes plates

**Types de formes** :

| Type | Description | Bords | Taille | Usage |
|------|------------|-------|--------|-------|
| **Éclaboussure** | Tache de peinture projetée. Forme centrale irrégulière avec pointes en étoile qui rayonnent. Gouttelettes satellites (3-8 petits blobs autour). Optionnel : une coulure qui dégouline. | Pointus, en étoile, chaotiques | 200-400px | Élément signature, 1-2 par section aux marges |
| **Vague organique** | Comme un nuage ou une dune, bord supérieur ou inférieur ondulé | Courbes fluides | 400-600px large | Fond de section, débordement entre sections |
| **Blob arrondi** | Forme de flaque, asymétrique | Courbes fluides | 200-400px | Derrière les contenus, coins des sections |
| **Virgule / touche** | Coup de pinceau plat court, arrondi aux extrémités | Courbes fluides | 60-120px | Détails, marges, groupés par 2-3 |

**Anatomie d'une éclaboussure SVG** :

```
                  gouttelette
                     ●
            pointe    ╱
              ╲      ╱
               ╲    ╱
    ●─── ───────╲──╱────────── ●  gouttelette
                 ╲╱
        ┌────────────────┐
        │  forme centrale │
        │   irrégulière   │
        └───────┬────────┘
                │
                │  coulure (optionnel)
                │
                ▼
```

Chaque éclaboussure est composée de :
1. **Forme centrale** : Un `<path>` fermé avec 8-15 pointes de longueur variable (certaines longues, certaines courtes)
2. **Gouttelettes** : 3-8 petits `<circle>` ou `<ellipse>` de 3-10px, dispersés autour (rayon 1.2-2x la taille de la forme centrale)
3. **Coulure** (optionnel, 1 éclaboussure sur 3) : Un `<path>` allongé qui part du bas de la forme, s'effilant vers le bas

**Principes de placement** :

- **Densité** : 2-3 formes par section (mélange de types), jamais plus de 4
- **Alternance des couleurs** : Alterner or/jaune et rose d'une section à l'autre, bordeaux en accent ponctuel
- **Position** : Toujours aux marges et dans les coins, jamais au centre. Certaines formes débordent de leur section (masquées par `overflow: hidden`)
- **Variété** : Ne pas répéter le même type de forme dans deux sections consécutives
- **Respiration** : Laisser au moins une section sur deux avec un placement plus léger (1 seule forme) pour éviter la surcharge
- **Aléatoire** : Le positionnement exact, les rotations et les tailles varient — pas de grille ni de pattern régulier

**Propriétés CSS communes** :

```css
.deco-shape {
	position: absolute;
	pointer-events: none;
	z-index: 0;
}

/* Le contenu textuel est TOUJOURS au-dessus,
   sur un fond opaque implicite ou explicite */
.deco-shape + .section__content,
.section__content {
	position: relative;
	z-index: 1;
}
```

**Responsive** :
- Mobile (< 768px) : Tailles réduites de 50%, opacités réduites de 20%
- Éclaboussures : conserver mais réduire les gouttelettes satellites (garder 2-3 au lieu de 5-8)
- Les vagues restent car elles contribuent à l'ambiance générale

### 2. Cicatrices dorées Kintsugi (couche intermédiaire)

**Principe** : Les lignes dorées droites (section dividers du PRD 0014) sont remplacées par des **cicatrices organiques** — des lignes irrégulières, d'épaisseur variable, qui ondulent librement comme la ligne dorée du logo Kiyose (`assets/images/logo-kiyose.png`). Elles évoquent les fissures réparées à l'or du Kintsugi.

**Style de référence** : La ligne dorée du logo — un trait qui démarre fin, s'épaissit, change de direction, avec des courbes naturelles et une petite goutte à une extrémité. Pas une ligne droite, pas une courbe régulière.

**Technique** : SVG `<path>` avec `stroke-width` variable (via `stroke-dasharray` ou plusieurs segments de largeurs différentes), `stroke-linecap: round`, couleur or miel `#E6A528`.

**Utilisations (avec parcimonie)** :

| Emplacement | Description | Comportement |
|-------------|-------------|--------------|
| **Sous la navigation** | Filigrane fin, comme actuellement | Statique, toujours visible |
| **Autour du logo** | Comme actuellement | Statique, toujours visible |
| **Autour de certains blobs texte** | Cicatrice qui épouse partiellement le contour d'un blob contenant du texte (ex: bloc témoignage, bloc CTA) | Statique ou léger reveal au scroll |

**Quantité** : Maximum 3-4 cicatrices sur toute la page d'accueil. Ne pas en abuser — elles doivent rester un accent précieux, pas un motif répétitif.

**Sur fond crème doré** : Ne pas placer de cicatrices — elles seraient invisibles, et les passer en bordeaux leur ferait perdre leur sens.

**Overlay hero** : L'overlay vitrail actuel est remplacé par un subtil halo doré :

```css
.hero-section--with-overlay::before {
	background:
		radial-gradient(ellipse at 65% 40%, rgb(230 165 40 / 10%) 0%, transparent 35%),
		radial-gradient(ellipse at 25% 60%, rgb(244 201 117 / 6%) 0%, transparent 30%);
}
```

### 3. Gribouillis, taches & virgules (couche détail dynamique)

**Principe** : Des éléments expressifs qui évoquent le geste spontané — le marqueur qu'on attrape pour griffonner, la tache de peinture qui éclabousse, le petit tourbillon de joie. Ces éléments sont GROS (pas des micro-éléments discrets) et apparaissent avec du mouvement.

**Catalogue d'éléments** (SVG, 40-150px) :

| Type | Description | Technique SVG | Taille |
|------|-------------|---------------|--------|
| **Spirale épaisse** | Tourbillon en trait de marqueur, 2-3 tours | `<path>` spirale, `stroke-width: 5-8px`, `stroke-linecap: round`, pas de `fill` | 80-120px |
| **Gribouillis / squiggle** | Ligne ondulée libre au marqueur | `<path>` avec 4-6 courbes bezier irrégulières, `stroke-width: 4-6px` | 100-150px |
| **Mini-éclaboussure** | Version réduite des éclaboussures de la couche 1 — forme en étoile irrégulière + 2-4 gouttelettes | `<path>` étoilé + `<circle>` satellites, `fill` uni | 50-90px |
| **Virgules groupées** | 2-3 coups de pinceau courts, parallèles mais pas alignés | `<path>` segments courts arrondis, `fill` uni | 40-80px (le groupe) |
| **Croix / astérisque épais** | Deux traits croisés, trait de marqueur | `<path>` deux segments croisés, `stroke-width: 5-7px` | 40-60px |

**Couleurs** : Piocher librement dans or `#E6A528`, jaune chaud `#FBBE2E`, rose `#D7A4A4` et bordeaux `#5D0505` (accent). Varier d'un élément à l'autre, ne pas répéter la même couleur sur deux éléments voisins.

### Comportement dynamique (scroll reveal + mouvement)

**Animation d'entrée** (quand l'élément entre dans le viewport) :

```
Chaque élément part de :
  opacity: 0
  transform: scale(0.6) rotate(-15deg)

Et arrive à :
  opacity: 1
  transform: scale(1) rotate(var(--deco-rotation))

Durée : 500ms
Easing : cubic-bezier(0.34, 1.56, 0.64, 1) — léger overshoot pour un effet "pop"
Décalage entre éléments : 150ms (effet cascade)
```

**Mouvement continu léger** (après apparition) :

Certains éléments ont une animation de "flottement" très lente pour donner de la vie à la page :

```css
@keyframes deco-float {
	0%, 100% { transform: translateY(0) rotate(var(--deco-rotation)); }
	50% { transform: translateY(-8px) rotate(calc(var(--deco-rotation) + 2deg)); }
}

.deco-element--float {
	animation: deco-float 6s ease-in-out infinite;
}
```

- Appliqué uniquement aux gribouillis et taches (pas aux grandes formes de fond)
- Mouvement subtil : 8px de déplacement vertical, 2° de rotation
- **Critère** : Maximum 3 éléments animés visibles simultanément à l'écran

**`prefers-reduced-motion: reduce`** :

```css
@media (prefers-reduced-motion: reduce) {
	.deco-element {
		/* Apparition instantanée, pas de transition */
		opacity: 1 !important;
		transform: none !important;
		transition: none !important;
		animation: none !important;
	}
}
```

### Parallax léger (optionnel)

Les grandes formes de fond (couche 1) peuvent avoir un léger effet parallax au scroll — elles bougent légèrement moins vite que le contenu, créant une sensation de profondeur.

- Décalage : `translateY(scroll * -0.05)` — très subtil
- Implémenté via `transform` dans le requestAnimationFrame du scroll observer
- **Désactivé avec `prefers-reduced-motion: reduce`**

## Direction artistique — ce qu'il faut faire et éviter

### Faire

| Faire | Pourquoi | Exemple |
|-------|----------|---------|
| Éclaboussures avec pointes en étoile et gouttelettes | Le geste spontané, l'accident heureux | `tache.png` |
| Trait de marqueur épais (5-8px) | L'imperfection du geste, énergie | La spirale bordeaux de l'image 2 |
| Haute opacité (30-60%) | Impact visuel, audace | Les formes des images de référence sont franches |
| Asymétrie, inclinaisons marquées | Rien d'aligné, esprit "fait main" | Tous les éléments sont de travers |
| Couleurs franches : or, bordeaux, rose | Cohérence avec l'identité Kiyose | Palette des 3 images |
| Gros éléments qui débordent | Générosité, exubérance | La vague qui sort du cadre |

### Éviter

| Éviter | Pourquoi | Faire plutôt |
|--------|----------|--------------|
| Formes géométriques (cercles, rectangles) | Trop propre, trop sage | Éclaboussures, blobs, vagues |
| Style Matisse / découpages composés | Trop artistique, trop réfléchi | Éclaboussures brutes, gestes spontanés |
| Pastels à basse opacité (5-15%) | Trop discret, manque d'impact | Couleurs franches à 30-60% |
| Symétrie, alignement parfait | Tue l'énergie "fait main" | Rotations, débordements, asymétrie |
| Petits éléments discrets | Trop timide | Éléments gros et assumés |
| Turquoise, vert, bleu froid | Hors palette de référence | Or, jaune, bordeaux, rose |

## Accessibilité (WCAG 2.2 AA)

### Mesures standard (inchangées)

- Tous les éléments décoratifs : `aria-hidden="true"` et `role="presentation"`
- `pointer-events: none` sur tous les éléments décoratifs
- Pas de mouvement automatique qui démarre sans action utilisateur (les animations au scroll sont déclenchées par l'utilisateur)
- Vérifier le contraste texte/fond y compris aux endroits où des formes de fond passent derrière le texte

### Points d'attention spécifiques à ce PRD

**1. Formes de fond à haute opacité sous le texte**

Les formes organiques de la couche 1 sont à 30-60% d'opacité. Si elles passent sous une zone de texte, elles modifient la couleur de fond effective et peuvent réduire le contraste.

**Règle** : Le conteneur de texte (`.section__content`) doit toujours avoir un `z-index` supérieur et un fond opaque (explicite ou implicite via le fond de section). Les formes de fond sont positionnées en `absolute` dans la section, derrière le contenu.

**Vérification** : Pour chaque section, vérifier que le ratio de contraste texte/fond reste ≥ 4.5:1 même si une forme est partiellement visible sous le texte.

**2. Animation continue (flottement)**

WCAG 2.2 SC 2.3.3 (AAA, recommandé) et SC 2.2.2 (AA) : les animations qui ne sont pas essentielles doivent pouvoir être mises en pause ou désactivées.

**Implémentation** :
- `prefers-reduced-motion: reduce` supprime TOUTES les animations continues
- Le mouvement de flottement est subtil (8px, 6s) et ne constitue pas un "clignotement" au sens WCAG
- Maximum 3 éléments animés simultanément visibles

**3. Parallax et troubles vestibulaires**

Le parallax peut provoquer des vertiges chez les personnes ayant des troubles vestibulaires.

**Implémentation** :
- Décalage très faible (`scroll * -0.05`)
- Désactivé avec `prefers-reduced-motion: reduce`

**4. Fond jaune/doré et cicatrices Kintsugi**

Sur les sections à fond crème doré (`#FDF0D5`), les cicatrices dorées ont un contraste insuffisant (~1.7:1) et deviennent peu visibles. Plutôt que de les dénaturer en changeant leur couleur, **ne pas placer de cicatrices sur les sections à fond chaud**. Les cicatrices restent toujours dorées — mieux vaut pas de cicatrice qu'une cicatrice qui n'est plus dorée.

## Performance

- SVG inline : Pas de requêtes HTTP supplémentaires
- Les grandes formes de fond sont en CSS (`clip-path` ou SVG inline) — coût GPU minimal
- Intersection Observer pour le scroll reveal — pas d'écouteurs scroll coûteux
- `will-change: opacity, transform` uniquement pendant les transitions, retiré après
- Animation `deco-float` : Utilise `transform` uniquement (propriété composite, pas de reflow)
- Mobile : Réduction du nombre d'éléments décoratifs (éclaboussures simplifiées, tailles réduites)
- Parallax : Limité à requestAnimationFrame, throttlé

## Fichiers impactés

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `assets/css/variables.css` | Ajouter `--kiyose-color-cream-gold` et `--kiyose-color-warm-yellow` ; supprimer `--kiyose-color-coral` et `--kiyose-color-turquoise` si déjà présents |
| `assets/css/components/kintsugi.css` | Remplacer la section "DECORATIVE BUBBLES" par le système de formes organiques + styles d'animation |
| `template-parts/content-service.php` | Supprimer les SVG `<circle>` des bulles |
| `templates/page-home.php` | Ajouter les formes organiques SVG, gribouillis et taches dans les sections |
| `inc/enqueue.php` | Enregistrer `decorative-reveal.js` |
| `doc/design-system.md` | Documenter les nouvelles couleurs et le système décoratif expressif |

### Fichiers créés

| Fichier | Contenu |
|---------|---------|
| `assets/js/modules/decorative-reveal.js` | Module Intersection Observer pour le scroll reveal avec mouvement (pop + float) |
| `template-parts/decorative-shapes.php` | Template part pour les formes organiques SVG : vagues, blobs, éclaboussures (paramétrable : type, couleur, taille, position, rotation) |
| `template-parts/decorative-scribbles.php` | Template part pour les gribouillis, mini-éclaboussures, virgules et spirales SVG |

### Fichiers supprimés (code supprimé)

| Code | Fichier |
|------|---------|
| SVG `<circle>` des bulles | `template-parts/content-service.php` |
| Styles `.bubble`, `.bubble--1`, `.bubble--2` | `assets/css/components/kintsugi.css` |

## Éléments conservés

- **Backgrounds alternés** (`.section--alt-bg`, `.section--tinted`) : Inchangés, mais possibilité d'utiliser `--kiyose-color-cream-gold` comme variante
- **Animations de cartes services** (`home-animations.js`) : Inchangées

## Vérification

1. **Rendu visuel** : `make start` → naviguer sur `http://127.0.0.1:8000` → vérifier les 3 couches décoratives
2. **Esprit "art expressif"** : L'ensemble évoque un atelier de peinture joyeux — éclaboussures, gribouillis, imperfection assumée. PAS un design propre, sage ou composé
3. **Luminosité** : La dominante or/jaune est bien présente et donne de la chaleur
4. **Responsive** : Tester à 320px, 768px, 1024px, 1280px — les éléments s'adaptent sans gêner la lecture
5. **Accessibilité** :
   - Activer `prefers-reduced-motion: reduce` → aucune animation, aucun parallax
   - Vérifier les contrastes texte aux endroits où les formes passent sous le contenu
   - Navigation clavier : les éléments décoratifs n'interceptent pas le focus
6. **PHPCS** : `make phpcs` → aucune violation
7. **Lint JS** : ESLint sur `decorative-reveal.js`
8. **Performance** : Lighthouse → vérifier que LCP n'est pas impacté par les SVG inline
