# Recette · Modifier la page d'accueil

**Objectif :** modifier le contenu éditorial de la page d'accueil (bloc bienvenue, mots-clés, slogans, sections Q&A, citation).

La page d'accueil est particulière : une grande partie de son contenu ne se saisit **pas** dans l'éditeur Gutenberg principal, mais dans des **champs personnalisés** (*meta fields*) situés à part.

## Étapes

1. Menu **Pages** → ouvre la page d'accueil (généralement intitulée **Accueil**, avec le template **Page d'accueil**).
2. Si tu viens seulement de choisir le template **Page d'accueil**, clique sur **Mettre à jour**, puis recharge l'éditeur.
3. Repère la section **Page d'accueil — Bienvenue & Overlay À propos**, située sous l'éditeur Gutenberg principal. Elle contient notamment :
   - **Bloc bienvenue** (titre, sous-titre, texte court, mots-clés, slogan)
   - **Contenu Q&A** (une série de questions et réponses)
   - **Contenu texte et citation**
   - **Overlay À propos**
4. Modifie les champs voulus.
5. Clique sur **Mettre à jour** (en haut à droite).

## Le bloc bienvenue en détail

Le bloc bienvenue est la toute première zone visible de la page d'accueil. Il contient :

| Champ | Rôle |
|---|---|
| **Titre** | Le grand titre d'accueil (affiché en écriture manuscrite Dancing Script). Court, 3-7 mots. Exemple : « Bienvenue à l'Atelier Kiyose ». |
| **Sous-titre** | Une ligne d'accroche sous le titre. Exemple : « Un espace pour explorer, créer, rire et transformer ». |
| **Texte** | Un paragraphe court de présentation (2-4 phrases). |
| **Mots-clés** | Liste de mots ou expressions qui résument les thèmes abordés (art-thérapie, rire, méditation…). S'affichent en puces roses arrondies. |
| **Slogan** | Une phrase finale forte (citation, invitation). |

## Les sections « Contenu »

La page d'accueil comporte plusieurs sections de contenu éditorial :

- **Contenu 1 (Q&A)** : une série de questions et réponses (par exemple : « Qu'est-ce que l'art-thérapie ? » / « À qui s'adresse un atelier ? »).
- **Contenu 2 (texte + citation)** : un paragraphe de texte plus dense suivi d'une citation et, si besoin, de son auteurice.

Les champs exacts sont regroupés dans la section **Page d'accueil — Bienvenue & Overlay À propos**.

### La citation du Contenu 2

Pour la citation du bloc **Contenu 2**, utilise les champs séparés :

| Champ | Exemple |
|---|---|
| **Citation** | `Heureux soient les fêlés, car ils laissent passer la lumière` |
| **Auteurice de la citation** | `Michel Audiard` |

Ne mets pas de guillemets dans le champ **Citation** : le thème les ajoute automatiquement au bon format. Si tu ne connais pas l'auteurice, laisse le champ **Auteurice de la citation** vide.

## Les sections automatiques (rien à faire)

Certaines sections de la page d'accueil s'alimentent **toutes seules** — tu n'as pas à les modifier sur la page d'accueil elle-même :

- Le **carrousel de témoignages** → se met à jour quand tu ajoutes un témoignage ([voir recette](ajouter-un-temoignage.md)).
- Les **prochains événements** → se met à jour quand tu ajoutes un événement via Events Manager.
- Les **derniers articles du blog** → se met à jour quand tu publies un article.
- L'**inscription newsletter** → formulaire Brevo (pas de modification côté page d'accueil).

## Pour aller plus loin

- [Le template « Page d'accueil »](../02-choisir-un-template.md#page-daccueil)
- [Gérer les témoignages](../05-temoignages.md)
- [Ajouter un événement](../06-evenements-calendrier.md)
