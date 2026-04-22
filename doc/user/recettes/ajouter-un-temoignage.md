# Recette · Ajouter un témoignage

**Objectif :** saisir un nouveau témoignage dans le carnet et vérifier qu'il apparaît sur le site.

## Étapes

1. Dans le menu de gauche de l'admin, clique sur **Témoignages**.
2. Clique sur **Ajouter** (en haut de la page).
3. Dans le champ **Titre**, tape le **prénom** de la personne (ou laisse vide pour un témoignage anonyme).
4. Dans la grande zone de contenu, colle la **citation**. Garde-la courte (2-4 lignes idéalement).
5. Dans le panneau latéral, cherche le champ **Contexte**. Choisis :
   - **Particulier** pour un client privé,
   - **Entreprise** pour une société,
   - **Structure** pour une association, école, etc.
6. Clique sur **Publier**.

## Résultat attendu

Le témoignage apparaît maintenant dans les endroits où le shortcode `[kiyose_testimonials]` est utilisé : carrousel de la page d'accueil, éventuellement sur d'autres pages si tu en as ajouté.

**Vérifier :** ouvre la page d'accueil du site (`/`) et fais défiler jusqu'à la section des témoignages. Tu dois voir ton témoignage apparaître dans la rotation.

> **Astuce :** le shortcode tire les témoignages au hasard. Recharge la page plusieurs fois pour voir si ton témoignage sort. Le paramètre `context` filtre par type : si tu as mis « Particulier » mais que la page d'accueil affiche un carrousel filtré sur « Entreprise », ton témoignage ne sortira pas là — il sortira ailleurs.

## Pour aller plus loin

- [Tous les paramètres du shortcode](../04-shortcodes.md#kiyose_testimonials--témoignages-en-grille-ou-carrousel)
- [Gestion complète des témoignages](../05-temoignages.md)
