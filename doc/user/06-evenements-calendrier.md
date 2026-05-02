# 06 · Événements et calendrier

Les événements (ateliers, stages, journées, tarifs) sont gérés par le plugin **Events Manager**. Le thème se contente d'afficher ce que le plugin produit.

## Ajouter un événement

1. Dans le menu de gauche de l'admin, clique sur **Événements**.
2. Clique sur **Ajouter un événement**.
3. Remplis les champs principaux :
   - **Titre** : nom de l'atelier (« Journée bols tibétains », « Atelier philo-art »…).
   - **Date et heure de début / fin**.
   - **Lieu** : choisis un lieu déjà enregistré (Brux, Poitiers, en ligne) ou crée-en un.
   - **Tarif** : montant en euros, ou précision (« Prix libre », « 20–40 € »).
   - **Description** : contenu libre décrivant l'atelier.
   - **Réservation** : si le plugin est configuré pour, tu peux activer les réservations en ligne (sans paiement).
4. Clique sur **Publier**.

L'événement apparaît automatiquement sur la page **Calendrier et tarifs** du site.

## Afficher la liste principale du calendrier

Sur la page **Calendrier et tarifs**, utilise ce shortcode :

```
[kiyose_events_list]
```

Il affiche les événements à venir avec Events Manager. Il est recommandé pour la page calendrier principale, car les boutons **Voir le calendrier** des pages de service peuvent lui envoyer un filtre de catégories.

Exemple : si la page **Art-thérapie** filtre sur la catégorie `art-therapie`, le bouton envoie ce filtre au calendrier. Si aucune catégorie n'est choisie sur une page de service, le calendrier affiche toutes les dates.

## Modifier un lieu

Menu **Événements** → **Lieux** (ou **Locations**). Tu peux créer un nouveau lieu ou modifier un existant.

## Limites du thème

Le thème **ne modifie pas** les fonctionnalités d'Events Manager. Les champs disponibles, la façon de réserver, les notifications par e-mail… tout cela est géré par le plugin.

**Pas de paiement en ligne :** le site ne gère pas de transaction financière. La réservation est une simple prise de contact.

## Afficher les événements ailleurs que sur la page calendrier

Tu peux utiliser des shortcodes Events Manager dans n'importe quelle page. Les plus courants :

- `[events_list limit="5"]` — liste des 5 prochains événements.
- `[events_calendar]` — un calendrier mensuel interactif.

Ces shortcodes ne reçoivent pas automatiquement les filtres envoyés par les pages de service. Pour le calendrier principal, préfère `[kiyose_events_list]`. Consulte la documentation officielle pour la liste complète :

📖 <https://wp-events-plugin.com/documentation/>

## Pour aller plus loin

- [Choisir le template « Calendrier et tarifs »](02-choisir-un-template.md)
- [Documentation Events Manager](https://wp-events-plugin.com/documentation/)
