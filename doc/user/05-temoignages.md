# 05 · Gérer les témoignages

Les témoignages sont un type de contenu à part (*Custom Post Type* en jargon technique). Ils se gèrent depuis un menu dédié dans l'admin, et s'affichent sur le site via le shortcode [`[kiyose_testimonials]`](04-shortcodes.md#kiyose_testimonials--témoignages-en-grille-ou-carrousel).

## Ajouter un témoignage

1. Dans le menu de gauche de l'admin, clique sur **Témoignages**.
2. Clique sur **Ajouter**.
3. Remplis les champs :
   - **Titre** : le **prénom** de la personne qui témoigne. Laisse vide si le témoignage est anonyme.
   - **Contenu** (l'éditeur central) : la **citation** elle-même. Évite les titres, les images, les tableaux — reste sur un ou deux paragraphes de texte.
   - **Contexte** (panneau latéral ou *meta box* dédiée) : choisis entre **Particulier**, **Entreprise**, **Structure**.
4. Clique sur **Publier**.

Le témoignage est immédiatement disponible pour être affiché via le shortcode `[kiyose_testimonials]`.

## Les trois contextes

Le **contexte** sert à filtrer les témoignages selon le public visé. Utilise-le de manière cohérente :

| Contexte | Valeur technique | Quand l'utiliser |
|---|---|---|
| **Particulier** | `individual` | Personne privée ayant participé à un atelier ou une séance individuelle. |
| **Entreprise** | `enterprise` | Retour d'une entreprise (séminaire, team-building). |
| **Structure** | `structure` | Retour d'une association, école, hôpital, centre social. |

Sur la page d'accueil, par exemple, on peut afficher uniquement les témoignages « Particulier » dans un carrousel, et sur la page « Entreprises » uniquement les contextes « Entreprise ».

## Modifier ou supprimer un témoignage

1. Menu **Témoignages** → **Tous les témoignages**.
2. Survole la ligne du témoignage à modifier.
3. Clique sur **Modifier** (pour corriger), **Corbeille** (pour supprimer).

Un témoignage dépublié (passé en brouillon) n'apparaît plus dans le shortcode.

## Afficher les témoignages sur une page

Utilise le shortcode `[kiyose_testimonials]`. Quelques exemples :

**Tous les témoignages en grille de 3 colonnes :**

```
[kiyose_testimonials display="grid" columns="3"]
```

**Carrousel de 5 témoignages « Entreprise » :**

```
[kiyose_testimonials display="carousel" limit="5" context="enterprise"]
```

La documentation complète du shortcode, avec tous les paramètres, est dans [04 · Les shortcodes](04-shortcodes.md#kiyose_testimonials--témoignages-en-grille-ou-carrousel).

## Bonnes pratiques

- **Demande l'autorisation** d'afficher un témoignage avant de le publier (RGPD + respect).
- **Reste court** : un témoignage dépasse rarement 3-4 lignes. Les longs blocs de texte sont tronqués dans le carrousel (un bouton « Voir plus » apparaît alors).
- **Relis-toi** : les fautes d'orthographe restent visibles publiquement jusqu'à ce que tu les corriges.

## Pour aller plus loin

- [Ajouter un témoignage (recette pas-à-pas)](recettes/ajouter-un-temoignage.md)
- [Le shortcode `[kiyose_testimonials]`](04-shortcodes.md#kiyose_testimonials--témoignages-en-grille-ou-carrousel)
