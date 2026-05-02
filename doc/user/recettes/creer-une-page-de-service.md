# Recette · Créer une page de service

**Objectif :** créer une nouvelle page qui présente un service (atelier, prestation) avec la bonne mise en page, un sommaire automatique et un encadré tarif.

## Étapes

1. Menu **Pages** → **Ajouter**.
2. Saisis le titre : par exemple **Atelier philo-art**.
3. Dans le panneau de droite, onglet **Page**, section **Modèle**, sélectionne **Page de service**.
4. Dans la section **Image mise en avant** (panneau latéral), téléverse une image qui sera affichée en grand en haut de la page.
5. Ajoute un premier bloc **Paragraphe** avec une introduction courte (2-3 phrases).
6. Ajoute un bloc **Code court** (via le bouton **+**) et colle `[kiyose_signets][/kiyose_signets]` dedans. Ce shortcode générera automatiquement un sommaire à partir des titres de niveau 2 de la page.
7. Structure la suite de ta page avec des blocs **Titre** de niveau **Titre 2** (H2). Chaque titre 2 deviendra une entrée du sommaire. Exemples :
   - « Pour qui ? »
   - « Déroulement »
   - « Matériel »
   - « Tarif »
8. Sous le titre « Tarif », ajoute un bloc **Code court** avec :
   ```
   [kiyose_callout titre="Tarif"]
   80 € la séance de 2 heures. Règlement par chèque ou virement.
   [/kiyose_callout]
   ```
9. Clique sur **Publier**, puis recharge l'éditeur si le panneau **Page de service — Calendrier** n'apparaît pas encore.
10. Optionnel : dans **Page de service — Calendrier**, sélectionne les catégories d'événements à afficher quand le visiteur clique sur **Voir le calendrier**. Ne sélectionne rien si cette page doit montrer toutes les dates.

Exemple :

```
Service Art-thérapie → cocher la catégorie Art-thérapie
Service général → ne rien cocher
```

## Résultat attendu

La page affiche :
- L'image en grand en haut.
- Ton introduction.
- Un sommaire cliquable généré automatiquement.
- Tes sections, chacune avec son titre.
- L'encadré doré avec le tarif.
- En bas : deux boutons **Me contacter** et **Voir le calendrier** (ajoutés automatiquement par le template si les pages cibles sont configurées dans le Customizer).

## Pour aller plus loin

- [Le template « Page de service »](../02-choisir-un-template.md#page-de-service)
- [Le shortcode `[kiyose_signets]`](../04-shortcodes.md#kiyose_signets--sommaire-de-navigation-dans-la-page)
- [Le shortcode `[kiyose_callout]`](../04-shortcodes.md#kiyose_callout--encadré-de-mise-en-évidence)
