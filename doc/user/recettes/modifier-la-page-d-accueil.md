# Recette · Modifier la page d'accueil

**Objectif :** modifier le contenu éditorial de la page d'accueil (bloc bienvenue, mots-clés, slogans, sections Q&A).

La page d'accueil est particulière : une grande partie de son contenu ne se saisit **pas** dans l'éditeur Gutenberg principal, mais dans des **champs personnalisés** (*meta fields*) situés à part.

## Étapes

1. Menu **Pages** → ouvre la page d'accueil (généralement intitulée **Accueil**, avec le template **Page d'accueil**).
2. Repère les sections particulières, situées **sous l'éditeur Gutenberg principal** ou dans le **panneau latéral droit** (selon la configuration). Elles s'appellent par exemple :
   - **Bloc bienvenue** (titre, sous-titre, texte court, mots-clés, slogan)
   - **Contenu Q&A** (une série de questions et réponses)
   - **Contenu texte et slogan**
   - **Hero** (ancien — à vérifier selon la version)
3. Modifie les champs voulus.
4. Clique sur **Mettre à jour** (en haut à droite).

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
- **Contenu 2 (texte + slogan)** : un paragraphe de texte plus dense suivi d'un slogan.

Les champs exacts dépendent de la configuration ACF. Repère-les dans l'éditeur de la page d'accueil et modifie les valeurs.

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
