# Recette · Créer une section en colonnes

**Objectif :** afficher trois blocs côte à côte sur ordinateur, qui se remettent en une seule colonne sur mobile.

## Option 1 : bloc Colonnes natif de Gutenberg (le plus simple)

1. Dans l'éditeur, ajoute un bloc **Colonnes**.
2. Choisis le nombre de colonnes (3 par exemple).
3. Dans chaque colonne, ajoute le contenu voulu (paragraphe, image, titre, bouton…).

Ce bloc fonctionne bien et gère déjà le passage en une seule colonne sur mobile.

## Option 2 : classe `grid` (pour plus de contrôle)

Cette méthode utilise les classes CSS du thème. Elle est utile si tu veux un comportement responsive spécifique (par exemple, passer à **une colonne** en dessous de 1024 px de large plutôt qu'en dessous de 600 px).

### Étapes

1. Ajoute un bloc **Groupe** (cherche « Groupe » dans la liste des blocs).
2. À l'intérieur du groupe, ajoute les blocs qui seront dans chaque colonne (par exemple trois blocs **Paragraphe**, ou trois blocs **Image** avec légende).
3. Clique sur le bloc **Groupe** (pas sur son contenu) pour le sélectionner.
4. Dans le panneau de droite, onglet **Bloc**, déplie la section **Avancé**.
5. Dans le champ **Classes CSS supplémentaires**, tape :
   ```
   grid grid--3-cols
   ```
6. Enregistre ou publie.

## Résultat attendu

Sur un écran d'ordinateur (≥ 1024 px), les trois blocs sont côte à côte.
Sur un écran plus petit (tablette ou mobile), ils s'empilent en une seule colonne.

## Variantes

- `grid grid--2-cols` : 2 colonnes à partir de la tablette.
- `grid grid--4-cols` : 4 colonnes à partir de l'ordinateur.
- `grid` (seul, sans variante) : 1 colonne (utile si tu veux juste un espacement régulier entre les blocs enfants).

## Pour aller plus loin

- [Toutes les classes de mise en page](../03-mise-en-page-avec-des-classes.md)
