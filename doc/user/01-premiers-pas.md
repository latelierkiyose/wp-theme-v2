# 01 · Premiers pas dans WordPress

Cette page explique le vocabulaire minimum et comment te repérer dans l'interface d'administration. Lis-la en entier si tu découvres WordPress.

## Se connecter

1. Ouvre `https://latelierkiyose.fr/wp-admin` dans ton navigateur.
2. Entre ton **identifiant** et ton **mot de passe**.
3. Tu arrives sur le **tableau de bord** (la page d'accueil de l'admin).

## Les trois zones à connaître

Une fois connectée :

1. **La barre latérale gauche** — le menu. C'est là que tu accèdes à tout : **Pages**, **Articles**, **Témoignages**, **Événements**, **Médias**, etc.
2. **La zone centrale** — le contenu qui change selon où tu es (liste de pages, éditeur, paramètres…).
3. **La barre du haut** — raccourcis (créer un contenu, voir le site, ton profil).

## Comprendre les types de contenu

WordPress range le contenu par types. Tu en utiliseras principalement quatre :

| Type | À quoi ça sert | Menu |
|---|---|---|
| **Page** | Contenu fixe du site : accueil, services, à propos, contact, mentions légales… | **Pages** |
| **Article** | Contenu daté : billets de blog, actualités | **Articles** |
| **Témoignage** | Un témoignage (prénom + citation + contexte) | **Témoignages** |
| **Événement** | Un atelier, une date au calendrier | **Événements** |

**Règle simple :** un contenu avec une date de publication (type « blog ») → **Article**. Un contenu permanent (« qui suis-je ? », « mes services ») → **Page**.

## L'éditeur Gutenberg

Quand tu crées ou modifies une Page ou un Article, tu entres dans l'éditeur **Gutenberg**. Il fonctionne par *blocs* : chaque paragraphe, image, titre ou bouton est un bloc indépendant que tu peux ajouter, déplacer ou supprimer.

### Ajouter un bloc

Clique sur le bouton **+** (en haut à gauche de l'éditeur, ou en ligne entre deux blocs) et choisis le type de bloc : **Paragraphe**, **Titre**, **Image**, **Colonnes**, **Bouton**, **Code court** (c'est là qu'on colle les *shortcodes*)…

### Les outils d'un bloc

Quand tu cliques sur un bloc :
- Une **petite barre d'outils** apparaît au-dessus (alignement, gras, italique, lien).
- Le **panneau de droite** (onglet **Bloc**) affiche les réglages détaillés du bloc sélectionné.

### Le champ « Classes CSS supplémentaires »

C'est un champ **très utile** pour appliquer une mise en forme particulière à un bloc. Il est caché par défaut :

1. Clique sur le bloc à modifier.
2. Dans le panneau de droite, onglet **Bloc**, déplie la section **Avancé** (tout en bas).
3. Tu vois un champ **Classes CSS supplémentaires**. Tape-y le nom d'une classe (par exemple `container` ou `grid grid--3-cols`).

La liste des classes disponibles est dans le fichier [03 · Mise en page avec des classes](03-mise-en-page-avec-des-classes.md).

## Enregistrer, prévisualiser, publier

En haut à droite de l'éditeur :

- **Enregistrer le brouillon** — sauvegarde ton travail sans le publier.
- **Prévisualiser** — ouvre un nouvel onglet avec le rendu tel qu'il sera affiché.
- **Publier** — met la page en ligne (visible par tout le monde). Un message de confirmation s'affiche.

**Bon à savoir :**
- Tu peux **dépublier** une page (la repasser en brouillon) à tout moment : en haut à droite, change le statut de **Publié** à **Brouillon**.
- Tu peux **planifier** une publication à une date future : clique sur la date à côté de **Publier**, choisis une date, puis clique sur **Planifier**.

## Pour aller plus loin

- [Choisir un template de page](02-choisir-un-template.md)
- [Les shortcodes du thème](04-shortcodes.md)
- [Glossaire](glossaire.md) — si un mot t'est inconnu
