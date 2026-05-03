# PRD 0063 — Durcissement sécurité des shortcodes et meta boxes

- **Statut** : terminé
- **Criticité** : moyenne
- **Version** : 1.1
- **Date** : 2026-05-03

## Historique

- **1.0** (2026-05-03) — Version initiale : bornes du shortcode `[kiyose_testimonials]` et validation du type des champs JSON dans les meta boxes.
- **1.1** (2026-05-03) — Ajout de deux items de durcissement issus d'un audit sécurité complémentaire : garde `defined('ABSPATH')` sur les fichiers PHP inclus, et note défensive sur `testimony-expand.js`.

## Contexte

Une revue sécurité du thème a identifié deux points de durcissement dans le code applicatif :

1. le shortcode `[kiyose_testimonials]` accepte une limite non bornée et utilise `orderby => rand` ;
2. certaines sauvegardes de meta boxes supposent que les champs JSON reçus depuis `$_POST` sont toujours des chaînes.

Le fichier `docker-compose.yaml` contient aussi des identifiants et ports de développement, mais il est explicitement hors périmètre de ce PRD car il est utilisé uniquement pour les tests locaux.

## Problème à résoudre

Le shortcode témoignages peut déclencher une requête coûteuse si une page publique utilise la valeur par défaut `limit="-1"` ou une limite très élevée. L'association d'une limite non bornée et d'un tri aléatoire est une surface de déni de service applicatif, même sans vulnérabilité SQL.

Les champs JSON des meta boxes d'accueil peuvent recevoir une forme inattendue, par exemple un tableau au lieu d'une chaîne. Dans ce cas, les fonctions typées `kiyose_sanitize_welcome_keyword_items()` et `kiyose_sanitize_qa_items()` peuvent produire une erreur fatale au lieu d'ignorer proprement l'entrée invalide. L'attaque nécessite un compte capable d'éditer la page et un nonce valide, mais le comportement reste fragile.

## Objectif

Durcir ces deux surfaces sans modifier l'expérience éditoriale normale :

1. borner les requêtes du shortcode témoignages ;
2. éviter le tri aléatoire coûteux par défaut ;
3. conserver une option contrôlée si un affichage aléatoire est réellement nécessaire ;
4. valider le type des champs JSON avant sanitization ;
5. ignorer les payloads invalides sans erreur fatale ;
6. couvrir les cas par tests automatisés.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Limite par défaut des témoignages | Remplacer `-1` par une valeur bornée | Évite les requêtes complètes sur les pages publiques |
| Limite maximale | Introduire une constante ou helper avec maximum explicite | Rend le plafond testable et facile à ajuster |
| Tri des témoignages | Utiliser un tri stable par défaut | Évite `ORDER BY RAND()` sur chaque rendu public |
| Aléatoire optionnel | Autoriser seulement une valeur explicite et plafonnée | Conserve le besoin éditorial éventuel sans coût non borné |
| Champs JSON meta boxes | Traiter uniquement les chaînes | Les tableaux ou objets POSTés sont invalides pour ces champs |
| Payload invalide | Sauvegarder une valeur vide ou ignorer selon le comportement existant le plus sûr | Empêche l'erreur fatale et garde une sortie frontend saine |
| Garde `defined('ABSPATH')` | Ajouter sur les fichiers PHP inclus directement (`inc/`, `template-parts/`, `templates/`) | Recommandation Theme Review Team WordPress.org, défense en profondeur si le serveur sert des `.php` du thème en direct |
| Pattern markup brut JS | Conserver `CLOSE_ICON_SVG` comme constante littérale uniquement | Le sink JS écrit du markup brut au DOM ; un futur refactor qui injecterait une donnée dynamique dégraderait silencieusement la sécurité |

## Spécifications

### 1. Borner `[kiyose_testimonials]`

Modifier `kiyose_testimonials_shortcode()` dans `latelierkiyose/inc/shortcodes.php`.

Règles attendues :

- la valeur par défaut de `limit` n'est plus `-1` ;
- toute valeur non numérique, nulle ou négative est remplacée par la limite par défaut ;
- toute valeur supérieure au maximum autorisé est ramenée au maximum ;
- `posts_per_page` reçoit toujours un entier positif borné ;
- la limite maximale est documentée dans le code par un nom explicite, par exemple `kiyose_get_testimonials_shortcode_max_limit()`.

Valeurs recommandées :

- limite par défaut : `6` ;
- limite maximale : `12`.

Ces valeurs peuvent être ajustées pendant l'implémentation si les tests ou la maquette montrent un besoin réel, mais elles doivent rester bornées.

### 2. Remplacer le tri aléatoire par défaut

Le shortcode ne doit plus utiliser `orderby => rand` par défaut.

Règles attendues :

- le tri par défaut est stable et prévisible, par exemple `date DESC` ou `menu_order ASC, date DESC` si un ordre éditorial est disponible ;
- si un tri aléatoire est conservé, il doit être opt-in via un attribut explicite, par exemple `ordre="aleatoire"` ou `random="true"` ;
- le tri aléatoire opt-in reste soumis au plafond de `limit` ;
- les attributs de tri sont sanitizés avec une liste blanche.

### 3. Valider les champs JSON des meta boxes

Modifier `kiyose_save_home_hero_meta()` dans `latelierkiyose/inc/meta-boxes.php`.

Champs concernés :

- `kiyose_welcome_keywords` ;
- `kiyose_content1_qa`.

Règles attendues :

- vérifier que la valeur `$_POST` est une chaîne avant `wp_unslash()` et avant l'appel aux fonctions typées ;
- si la valeur n'est pas une chaîne, la traiter comme une entrée invalide ;
- ne jamais produire d'erreur fatale sur tableau, objet ou valeur inattendue ;
- continuer à préserver les accents et échappements JSON valides ;
- conserver les protections existantes : nonce, autosave, capability, template requis.

### 4. Documentation utilisateur

Mettre à jour `doc/user/04-shortcodes.md`.

À documenter :

- la nouvelle limite par défaut de `[kiyose_testimonials]` ;
- la limite maximale ;
- le comportement du tri par défaut ;
- l'attribut éventuel pour demander un ordre aléatoire.

Aucune documentation utilisateur n'est nécessaire pour la validation interne des meta boxes, sauf si l'implémentation modifie le comportement visible dans l'admin.

### 5. Garde `defined('ABSPATH')` sur les fichiers PHP inclus

Ajouter la garde `defined( 'ABSPATH' ) || exit;` au début de chaque fichier PHP qui peut être inclus directement.

Fichiers concernés :

- tous les fichiers de `latelierkiyose/inc/*.php` ;
- tous les fichiers de `latelierkiyose/template-parts/*.php` ;
- tous les fichiers de `latelierkiyose/template-parts/home/*.php` ;
- tous les fichiers de `latelierkiyose/templates/*.php`.

Fichiers exclus (déjà gérés par le cœur de WordPress) :

- `latelierkiyose/functions.php` (chargé directement par WordPress) ;
- les templates racine (`header.php`, `footer.php`, `index.php`, `single.php`, `page.php`, `archive.php`, `home.php`, `404.php`, `search.php`, `searchform.php`).

Règles attendues :

- la ligne `defined( 'ABSPATH' ) || exit;` est placée juste après le tag PHP d'ouverture et le docblock du fichier ;
- la ligne respecte les conventions WPCS (espaces autour des parenthèses, point-virgule final) ;
- aucun comportement runtime n'est modifié quand le fichier est inclus normalement par WordPress.

Justification : le but est de garantir un échec immédiat si une mauvaise configuration serveur sert un fichier `.php` du thème en accès direct. Avec la configuration actuelle Apache/nginx, le risque est théorique, mais c'est une exigence du Theme Review Team WordPress.org et un coût marginal.

### 6. Note défensive sur `testimony-expand.js`

Modifier `latelierkiyose/assets/js/modules/testimony-expand.js`.

Contexte : à la ligne 133, le module assigne la constante `CLOSE_ICON_SVG` (définie L12-13) à la propriété de markup brut du bouton de fermeture. **Aucun flux de données utilisateur n'alimente cette assignation aujourd'hui** — la constante est un littéral SVG figé. La revue ne signale pas une vulnérabilité réelle, mais une fragilité défensive : si un futur refactor remplace la constante par une valeur dynamique, la sécurité dégraderait silencieusement.

Deux options acceptables — une seule est requise :

- **Option A (préférée si elle ne dégrade pas la lisibilité)** : remplacer l'assignation de markup brut par une construction DOM via `document.createElementNS('http://www.w3.org/2000/svg', ...)` et `appendChild`, ou par le clonage d'un `<template>`.
- **Option B** : conserver l'assignation et ajouter un commentaire d'invariant immédiatement au-dessus, par exemple :
  ```javascript
  // INVARIANT: CLOSE_ICON_SVG must remain a literal constant. Do not feed dynamic data into this assignment.
  ```

La règle la plus économique est l'option B si l'option A complique la lecture.

## Accessibilité et sécurité

- Aucune sortie HTML supplémentaire ne doit être introduite sans échappement.
- Le shortcode doit rester utilisable par les rédacteurices sans créer de charge serveur disproportionnée.
- Les champs admin restent protégés par nonce et capability.
- Les entrées inattendues sont rejetées à la frontière de sauvegarde.
- Aucun secret ni donnée sensible n'est manipulé.

## Hors périmètre

- Modifier le modèle de données du CPT `kiyose_testimony`.
- Ajouter une interface d'administration dédiée pour ordonner les témoignages.
- Changer le rendu visuel de la grille ou du carousel.
- Modifier les shortcodes Events Manager ou Brevo.
- Modifier `docker-compose.yaml`.

## Tests et validation

### Tests automatisés

- [ ] `[kiyose_testimonials]` utilise une limite par défaut positive et bornée.
- [ ] `[kiyose_testimonials limit="-1"]` ne lance pas une requête non bornée.
- [ ] `[kiyose_testimonials limit="999"]` est plafonné au maximum autorisé.
- [ ] `[kiyose_testimonials limit="abc"]` revient à la limite par défaut.
- [ ] Le tri par défaut du shortcode n'utilise pas `rand`.
- [ ] Le tri aléatoire, s'il existe encore, est opt-in et plafonné.
- [ ] La sauvegarde de `kiyose_welcome_keywords` avec une valeur POST tableau ne produit pas d'erreur fatale.
- [ ] La sauvegarde de `kiyose_content1_qa` avec une valeur POST tableau ne produit pas d'erreur fatale.
- [ ] Les champs JSON valides continuent à préserver les accents après sauvegarde/relecture.

### Tests structurels (non automatisés)

- [ ] `grep -L "ABSPATH" latelierkiyose/inc/*.php latelierkiyose/template-parts/*.php latelierkiyose/template-parts/home/*.php latelierkiyose/templates/*.php` retourne une sortie vide (tous les fichiers inclus directement portent la garde).
- [ ] Le fichier `latelierkiyose/assets/js/modules/testimony-expand.js` contient soit un commentaire `INVARIANT` au-dessus de la ligne 133, soit une construction DOM en lieu et place de l'assignation de markup brut.

### Validation finale

- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `./bin/phpcs.sh` passe.
- [ ] `make test` passe.
- [ ] `doc/user/04-shortcodes.md` est mis à jour si le comportement du shortcode change.
