# PRD 0043 — Meta boxes visibles uniquement sur leur template

## Statut

**terminé**

## Contexte

Dans l'éditeur des pages, deux meta boxes spécifiques étaient enregistrées sur toutes les pages :

- **Page d'accueil — Bienvenue & Overlay À propos**
- **Page de contact — Photo**

Les champs étaient masqués quand le template ne correspondait pas, mais les panneaux restaient présents avec un message explicatif. Pour une rédactrice, cela ajoute du bruit dans l'éditeur et laisse croire que ces champs peuvent concerner n'importe quelle page.

## Objectif

N'afficher chaque meta box que sur la page qui utilise son template :

| Meta box | Template requis |
|---|---|
| Page d'accueil — Bienvenue & Overlay À propos | `templates/page-home.php` |
| Page de contact — Photo | `templates/page-contact.php` |

## Décisions

- La meta box doit être **absente** hors de son template, pas simplement masquée.
- Le changement de template dans Gutenberg peut nécessiter **enregistrement puis rechargement** de l'éditeur pour faire apparaître les champs spécifiques.
- Les clés de meta existantes restent inchangées.
- Les garde-fous de sauvegarde existants restent obligatoires : nonce, autosave, capacité `edit_post`, vérification du template avant écriture.

## Spécifications

### 1. Enregistrement conditionnel

Dans `latelierkiyose/inc/meta-boxes.php` :

- Ajouter un helper testable `kiyose_page_uses_template( $post, $template )`.
- Enregistrer les meta boxes via le hook `add_meta_boxes_page`, qui reçoit la page courante.
- Ne pas appeler `add_meta_box()` si `_wp_page_template` ne correspond pas au template attendu.

### 2. Rendu admin

- Supprimer les notices expliquant que le panneau deviendra actif après sélection du template.
- Supprimer le JavaScript de bascule des champs selon `#page_template`.
- Conserver le JavaScript utile aux champs eux-mêmes : repeaters de la home et media uploader.

### 3. Documentation

Mettre à jour la documentation utilisateur pour préciser que les champs spécifiques n'apparaissent qu'après avoir assigné le bon template, enregistré, puis rechargé si nécessaire.

## Hors périmètre

- Pas de modification front-office.
- Pas de migration de contenu.
- Pas de changement des clés post meta.
- Pas de nouvelle option d'administration.

## Tests et validation

### Tests automatisés

- [x] `kiyose_page_uses_template()` retourne `false` si aucun post n'est fourni.
- [x] `kiyose_page_uses_template()` retourne `true` si le template correspond.
- [x] `kiyose_page_uses_template()` retourne `false` si le template diffère.
- [x] La meta box home est enregistrée uniquement pour `templates/page-home.php`.
- [x] La meta box contact est enregistrée uniquement pour `templates/page-contact.php`.

### Validation manuelle admin

- [ ] Page standard : aucune des deux meta boxes spécifiques n'est visible.
- [ ] Page avec template **Page d'accueil** : seule la meta box accueil est visible.
- [ ] Page avec template **Page de contact** : seule la meta box contact est visible.
- [ ] Après changement de template dans Gutenberg, sauvegarder et recharger fait apparaître la bonne meta box.

### Standards

- [x] `make test` passe.
