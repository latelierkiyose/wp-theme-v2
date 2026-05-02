# Recette · Ajouter un appel à l'action

**Objectif :** ajouter un bouton doré centré pour inviter la personne à faire une action précise : réserver, contacter, consulter une page.

## Étapes

1. Ouvre la page ou l'article à modifier.
2. Place ton curseur à l'endroit où tu veux afficher le bouton.
3. Ajoute un bloc **Code court**.
4. Colle dedans :
   ```
   [kiyose_cta texte="Réserver votre appel découverte" lien="/contact/"]
   ```
5. Remplace le texte entre guillemets si besoin.
6. Remplace le lien entre guillemets si besoin.
7. Enregistre ou publie.

## Résultat attendu

Un bouton **doré**, avec texte **bordeaux**, apparaît au centre de la page. Il a le même rendu que les boutons d'appel à l'action de la page d'accueil.

## Exemples

### Réserver un appel découverte

```
[kiyose_cta texte="Réserver votre appel découverte" lien="/contact/"]
```

### Voir les prochaines dates

```
[kiyose_cta texte="Voir les prochaines dates" lien="/calendrier-tarifs/"]
```

### Découvrir une pratique

```
[kiyose_cta texte="Découvrir l'art-thérapie" lien="/art-therapie/"]
```

## Astuces

- **Garde un texte court.** Le bouton doit rester facile à lire.
- **Utilise une action claire.** Commence par un verbe : « Réserver », « Découvrir », « Voir », « Me contacter ».
- **Vérifie le lien.** Après publication, clique sur le bouton pour confirmer qu'il mène à la bonne page.
- **N'en mets pas trop.** Un bouton principal par grande section suffit souvent.

## Pour aller plus loin

- [Documentation complète de `[kiyose_cta]`](../04-shortcodes.md#kiyose_cta--bouton-dappel-à-laction)
