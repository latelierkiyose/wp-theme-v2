# Recette · Ajouter un sommaire automatique

**Objectif :** afficher en haut d'une page longue un sommaire cliquable qui pointe vers chaque section.

## Étapes (sommaire automatique)

1. Ouvre la page à modifier dans l'éditeur.
2. Place ton curseur à l'endroit où tu veux afficher le sommaire (en général, juste après l'introduction).
3. Ajoute un bloc **Code court**.
4. Colle dedans :
   ```
   [kiyose_signets][/kiyose_signets]
   ```
5. Vérifie que ta page contient des blocs **Titre** de niveau **Titre 2** (H2). Ce sont ces titres qui deviendront les entrées du sommaire.
6. Enregistre ou publie.

## Résultat attendu

En haut de la page, une barre apparaît avec un bouton par section (un par titre H2). Cliquer sur un bouton fait défiler la page jusqu'à la section correspondante.

## Variante : sommaire manuel

Si tu veux choisir précisément les liens (par exemple, n'afficher que certaines sections), utilise le mode manuel :

```
[kiyose_signets]
Bienvenue | #intro
Matériel requis | #materiel
Étapes | #etapes
[/kiyose_signets]
```

Chaque ligne suit le format **Texte à afficher | #identifiant-de-la-section**.

Pour que les liens fonctionnent, les sections cibles doivent avoir le même identifiant :

1. Sélectionne un bloc **Titre** (par exemple, le titre « Matériel requis »).
2. Dans le panneau de droite, section **Avancé**, remplis **Ancre HTML** avec la valeur correspondante (ici : `materiel`, sans le `#`).

## Pour aller plus loin

- [Documentation complète de `[kiyose_signets]`](../04-shortcodes.md#kiyose_signets--sommaire-de-navigation-dans-la-page)
- [Créer une page de service](creer-une-page-de-service.md) — exemple concret
