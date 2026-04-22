# Recette · Mettre en avant une information

**Objectif :** attirer l'œil sur une information importante (tarif, horaire, mise en garde, astuce) avec un encadré visuellement distinct.

## Étapes

1. Ouvre la page à modifier.
2. Place ton curseur à l'endroit où tu veux l'encadré.
3. Ajoute un bloc **Code court**.
4. Colle dedans :
   ```
   [kiyose_callout titre="Bon à savoir"]
   Ton texte important ici. Plusieurs paragraphes sont possibles.
   [/kiyose_callout]
   ```
5. Remplace **Bon à savoir** par le titre de ton choix (ou laisse vide pour un encadré sans titre).
6. Remplace **Ton texte important ici…** par ton contenu.
7. Enregistre ou publie.

## Résultat attendu

Un encadré **crème doré** avec une **bordure gauche dorée** apparaît. Le titre (s'il y en a un) est en gras au-dessus du contenu.

## Variantes

### Sans titre

```
[kiyose_callout]
Les places sont limitées à 8 personnes par atelier.
[/kiyose_callout]
```

### Avec un lien

```
[kiyose_callout titre="Pour aller plus loin"]
Toutes les dates sont dans la [page du calendrier](/calendrier).
[/kiyose_callout]
```

### Avec plusieurs paragraphes

```
[kiyose_callout titre="À prévoir"]
Des vêtements confortables dans lesquels tu te sens libre de bouger.

Une petite bouteille d'eau, de quoi prendre des notes si tu le souhaites.
[/kiyose_callout]
```

## Astuces

- **Évite d'en abuser.** Si chaque section a son encadré, l'effet « je mets en avant l'important » se dilue. Deux ou trois encadrés max par page.
- **Garde le titre court.** Deux à quatre mots idéalement (« Tarif », « Bon à savoir », « À prévoir »).
- **Le contenu reste court aussi.** L'encadré attire l'œil — si le texte est long, le lecteur est rebuté.

## Pour aller plus loin

- [Documentation complète de `[kiyose_callout]`](../04-shortcodes.md#kiyose_callout--encadré-de-mise-en-évidence)
