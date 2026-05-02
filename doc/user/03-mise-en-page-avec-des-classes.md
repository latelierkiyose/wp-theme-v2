# 03 · Mise en page avec des classes

Cette page liste les **classes CSS** que tu peux appliquer à un bloc Gutenberg pour modifier sa mise en page. Utilise-les dans le champ **Classes CSS supplémentaires**.

## Où coller une classe CSS

1. Clique sur le bloc que tu veux styler (un bloc **Groupe**, **Colonnes**, **Paragraphe**…).
2. Dans le panneau de droite, onglet **Bloc**, déplie la section **Avancé** (tout en bas).
3. Dans le champ **Classes CSS supplémentaires**, tape le nom de la classe.
4. Tu peux combiner plusieurs classes en les séparant par un espace : `grid grid--3-cols`.

> **Règle d'or :** tape les classes **sans le point** (`container`, pas `.container`). Le point ne sert que dans la documentation pour dire « c'est une classe CSS ».

## Les classes disponibles, par intention

### Contenir un contenu sur une largeur confortable

| Classe | Rendu |
|---|---|
| `container` | Centre le contenu avec une largeur maximale (1600 px) et un espacement sur les côtés qui s'adapte au mobile, tablette et ordinateur. |

**Où l'utiliser :** sur un bloc **Groupe** qui doit cadrer une section sur toute la largeur de la page sans toucher les bords.

**Exemple :** `container`

### Mettre le contenu en colonnes

| Classe | Rendu |
|---|---|
| `grid` | Transforme l'intérieur du bloc en grille. Par défaut : **une seule colonne**. |
| `grid grid--2-cols` | Deux colonnes à partir de 768 px (tablette et ordinateur). Sur mobile : une colonne. |
| `grid grid--3-cols` | Trois colonnes à partir de 1024 px (ordinateur). Sur tablette et mobile : une colonne. |
| `grid grid--4-cols` | Quatre colonnes à partir de 1024 px. Sur tablette et mobile : une colonne. |

**Où l'utiliser :** sur un bloc **Groupe** contenant plusieurs blocs à mettre côte à côte (cartes, icônes + texte, deux paragraphes parallèles…).

**Exemple :** `grid grid--3-cols`

> **Remarque :** WordPress a aussi un bloc **Colonnes** natif. Il fait très bien le travail pour deux à six colonnes réglables. Utilise `grid grid--N-cols` seulement si tu veux le comportement responsive décrit ci-dessus (colonnes qui se remettent en une seule sur mobile).

### Ajouter un fond coloré à une section

| Classe | Rendu |
|---|---|
| `section--alt-bg` | Fond blanc pur. |
| `section--tinted` | Fond rose-gris très léger (transparence 10 %). |
| `section--with-overlay` | Ajoute un halo doré subtil (radial-gradient) pour un effet prestige. |

**Où l'utiliser :** sur un bloc **Groupe** qui constitue une section entière, pour la distinguer visuellement des sections voisines.

**Exemple :** `section--tinted`

### Séparer deux sections par une ligne dorée

Ces classes ajoutent un **séparateur horizontal doré** avec transition de couleur entre le haut et le bas du séparateur. Elles dessinent la petite ligne subtile inspirée du *kintsugi* (les cicatrices dorées).

| Classe | Transition de fond |
|---|---|
| `section-divider` | Transition par défaut : blanc → beige. |
| `section-divider section-divider--beige-to-white` | Beige → blanc. |
| `section-divider section-divider--white-to-tinted` | Blanc → teinté rose. |
| `section-divider section-divider--tinted-to-white` | Teinté rose → blanc. |
| `section-divider section-divider--tinted-to-beige` | Teinté rose → beige. |

**Où l'utiliser :** sur un bloc **Groupe** vide placé **entre** deux sections qui ont des fonds différents. Le séparateur sert de transition visuelle entre elles.

**Exemple :** `section-divider section-divider--white-to-tinted`

### Masquer un texte mais le garder lisible par un lecteur d'écran

| Classe | Rendu |
|---|---|
| `sr-only` | Le texte devient **invisible à l'œil** mais **reste lu par les lecteurs d'écran**. Utile pour l'accessibilité. |

**Où l'utiliser :** sur un bloc **Paragraphe** qui contient un complément d'information utile uniquement pour les personnes non-voyantes (par exemple, un libellé décrivant une icône).

**Exemple :** `sr-only`

## Les boutons

Pour créer un bouton, **utilise le bloc Bouton de Gutenberg** — il est stylé automatiquement aux couleurs du thème (rose poudré + bordeaux). Pas besoin de classe CSS supplémentaire.

**Pour obtenir un bouton « secondaire » (contour seulement, sans fond)** :

1. Sélectionne le bloc **Bouton** dans l'éditeur.
2. Dans le panneau de droite, onglet **Bloc**, regarde la section **Style**.
3. Clique sur la variante **Outline**.

Le bouton passe en style contour.

> **Évite** de taper `button--primary` ou `button--secondary` dans **Classes CSS supplémentaires** : ces classes ne fonctionnent que dans certains contextes très précis du thème (pied de page des services, page 404). Le bloc Bouton natif est ce qu'il te faut.

## Les images rondes

Pour créer une image ronde dans un article ou une page, **utilise le style `Ronde` du bloc Image**. Tu n'as pas besoin de taper une classe CSS.

1. Ajoute ou sélectionne un bloc **Image**.
2. Dans le panneau de droite, onglet **Bloc**, ouvre la section **Styles**.
3. Choisis le style **Ronde**.
4. Choisis l'alignement :
   - **Gauche** ou **Droite** si tu veux que le texte passe autour de l'image sur ordinateur.
   - **Centre** si tu veux une image ronde centrée, sans habillage du texte.

Sur tablette et mobile, l'image ronde revient automatiquement dans la colonne du texte : le texte ne passe pas autour, pour garder une lecture confortable.

**Exemple copiable :**

| Réglage | Valeur |
|---|---|
| Bloc | Image |
| Style | `Ronde` |
| Alignement | `Gauche` ou `Droite` |
| Texte alternatif | `Portrait de Sandrine Delmas` si l'image présente une personne |

**Conseils :**

- Choisis une photo déjà bien centrée, surtout pour un portrait : le style rond recadre l'image en carré.
- Renseigne un **texte alternatif** utile si l'image apporte une information. Si elle est seulement décorative, laisse le champ vide.
- Garde la légende courte, ou supprime-la, si tu veux un habillage du texte bien régulier autour du cercle.

## Règle importante

Si tu as besoin d'un style qui n'existe pas dans cette liste, **ne crée pas de style en ligne dans l'éditeur** (tu pourrais le faire mais ce n'est pas une bonne idée — le style sera perdu ou incohérent). Demande plutôt à ton développeur d'ajouter une nouvelle classe réutilisable. Elle sera documentée ici.

## Pour aller plus loin

- [Créer une section en colonnes (recette)](recettes/creer-une-section-en-colonnes.md)
- [Les shortcodes du thème](04-shortcodes.md)
- [Glossaire](glossaire.md)
