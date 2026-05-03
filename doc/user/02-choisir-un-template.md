# 02 · Choisir un template de page

Cette page liste les *templates* (modèles de page) disponibles et explique comment en assigner un à une page.

## Qu'est-ce qu'un template ?

Un *template* est un **modèle de mise en page**. C'est lui qui décide :
- Où est placé le titre.
- Si la page a une image à côté du texte (contact), des colonnes, un pied de page avec des boutons, etc.
- Quelles informations spécifiques sont demandées (champs personnalisés).

Le contenu que tu tapes dans l'éditeur s'insère **dans** le template. Changer de template change la présentation, pas le texte.

## Comment choisir un template

1. Ouvre ta page dans l'éditeur Gutenberg.
2. Dans le panneau de droite, clique sur l'onglet **Page** (pas **Bloc**).
3. Cherche la section **Modèle**.
4. Clique sur le nom du modèle actuel — une liste déroulante s'ouvre.
5. Sélectionne le template adapté.
6. **Enregistre** ou **Publie** la page.
7. Si le template ajoute des champs spécifiques, **recharge l'éditeur** pour les faire apparaître.

> **Astuce :** si tu ne vois pas la section **Modèle**, c'est que tu es sur l'onglet **Bloc**. Clique sur un espace vide de la page pour désélectionner le bloc en cours, puis bascule sur l'onglet **Page**.

## Les templates disponibles

| Template | Utilisation | Page correspondante |
|---|---|---|
| **Page d'accueil** | Unique page d'accueil du site. À n'utiliser qu'une seule fois. Contient un bloc bienvenue éditable, un carrousel de témoignages, les événements à venir, etc. | `/` |
| **Page de service** | Pour chaque service proposé (art-thérapie, rigologie, bols tibétains, ateliers philo). Pied de page automatique avec deux boutons : **Me contacter** et **Voir le calendrier**. | `/art-therapie`, `/rigologie`, `/bols-tibetains`, `/ateliers-philo` |
| **À propos** | Page de présentation de Virginie. Mise en page centrée, texte long. | `/a-propos` |
| **Page de contact** | Formulaire de contact + coordonnées + photo optionnelle. | `/contact` |
| **Calendrier et tarifs** | Affiche le calendrier des ateliers via Events Manager. | `/calendrier` |
| *(Modèle par défaut)* | Pour les pages standard (mentions légales, politique de confidentialité). N'apparaît pas dans la liste — c'est ce que WordPress utilise si tu ne choisis rien. | Pages légales choisies dans le Customizer |

## Détails par template

### Page d'accueil
- Contient un **bloc bienvenue** éditable (titre, sous-titre, texte court, mots-clés, slogan).
- Affiche automatiquement : le carrousel de témoignages, les prochains événements, la newsletter, les derniers articles.
- Les champs spécifiques se remplissent dans un panneau (*meta box*) sous l'éditeur principal. Si tu viens de choisir ce template, enregistre puis recharge la page pour voir ce panneau.
- Voir la recette : [Modifier la page d'accueil](recettes/modifier-la-page-d-accueil.md).

### Page de service
- Le contenu se rédige intégralement dans l'éditeur Gutenberg.
- En bas de page, deux boutons sont ajoutés automatiquement : **Me contacter** et **Voir le calendrier**.
- Les pages cibles de ces boutons se règlent dans **Apparence → Personnaliser → Pages de service — boutons**. Si un bouton n'apparaît pas, vérifie que sa page cible est bien configurée et publiée.
- Tu peux limiter le bouton **Voir le calendrier** à certaines catégories d'événements depuis le champ **Page de service — Calendrier** sous l'éditeur. Si tu ne sélectionnes rien, le calendrier affiche toutes les dates.
- L'image mise en avant (*featured image*) apparaît en grand en haut de la page.
- Recommandé d'ajouter un shortcode `[kiyose_signets]` en haut pour un sommaire automatique.
- Voir la recette : [Créer une page de service](recettes/creer-une-page-de-service.md).

### À propos
- Contenu libre dans Gutenberg, mise en forme centrée.
- Aucune fonctionnalité supplémentaire — c'est une page « texte long » bien présentée.

### Page de contact
- Un champ optionnel **Photo de contact** permet d'afficher un portrait au-dessus des coordonnées. Si tu viens de choisir ce template, enregistre puis recharge la page pour voir ce champ.
- Le formulaire est géré par le plugin **Contact Form 7** : son shortcode est déjà placé dans le template, tu n'as rien à faire côté formulaire.

### Calendrier et tarifs
- Le plugin **Events Manager** affiche automatiquement les événements à venir.
- Le shortcode recommandé sur cette page est `[kiyose_events_list]`, car il peut recevoir les filtres de catégories envoyés depuis les pages de service.
- Tu peux ajouter du contenu libre (intro, règlement, modalités de paiement) dans l'éditeur Gutenberg — il s'insère au-dessus ou autour du calendrier.

### Modèle par défaut
- Utilisé automatiquement si tu ne choisis pas de template.
- Affiche simplement le titre de la page et son contenu. Idéal pour : mentions légales, politique de confidentialité, toute page « texte pur » sans fonctionnalité particulière.
- Les pages « Mentions légales » et « Politique de confidentialité » restent des pages WordPress standard : tu peux modifier leur slug sans modifier le thème.

## Régler les liens légaux du footer

Les liens du footer vers les pages légales se règlent une seule fois dans le Customizer :

1. Ouvre **Apparence → Personnaliser → Footer — liens légaux**.
2. Dans **Page « Mentions légales »**, choisis la page publiée qui contient les mentions légales.
3. Dans **Page « Politique de confidentialité »**, choisis la page publiée qui contient la politique de confidentialité.
4. Clique sur **Publier**.

Exemple : si tu renommes ensuite l'URL de la page « Mentions légales » de `/mentions-legales/` vers `/informations-legales/`, le footer continue de pointer vers la bonne page automatiquement.

## Ce qui est automatique (pas à choisir)

Certaines pages utilisent des templates **automatiques** — tu n'as rien à sélectionner :

- **Articles de blog** (pages individuelles) → template `single.php`
- **Page principale des articles** (ex. `/actus-blog/`) → template automatique `home.php`, configuré dans **Réglages → Lecture**
- **Liste d'articles** (par catégorie, date, tag…) → template `archive.php`
- **Résultats de recherche** → template `search.php`
- **Page 404** (URL inexistante) → template `404.php`

Tu n'as jamais à choisir ces templates, ils s'appliquent tout seuls.

## Pour aller plus loin

- [Créer une page de service (recette)](recettes/creer-une-page-de-service.md)
- [Modifier la page d'accueil (recette)](recettes/modifier-la-page-d-accueil.md)
- [Les classes de mise en page](03-mise-en-page-avec-des-classes.md)
