# PRD 0047 — Refactor des meta boxes d'administration

- **Statut** : prêt à l'implémentation
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

`latelierkiyose/inc/meta-boxes.php` fait 914 lignes et concentre :

- les fonctions de sanitization ;
- le rendu HTML des champs ;
- le CSS inline d'administration ;
- le JavaScript inline pour repeaters et media uploader ;
- les handlers de sauvegarde ;
- la logique de visibilité selon le template de page.

Le fichier est difficile à faire évoluer. Ajouter un champ augmente mécaniquement la dette, et les comportements JavaScript ne sont ni testables ni réutilisables.

## Objectif

Séparer les responsabilités des meta boxes : helpers de rendu, sanitization/sauvegarde, styles admin et scripts admin. Le comportement fonctionnel dans l'éditeur WordPress doit rester identique.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| PHP | Garder `inc/meta-boxes.php` comme module principal, mais extraire des helpers internes | Évite un refactor trop large |
| CSS admin | Créer `assets/css/admin/meta-boxes.css` | Supprime les styles inline et mutualise le rendu |
| JS admin | Créer `assets/js/admin/meta-boxes.js` | Rend le comportement maintenable et lintable |
| Enqueue admin | Ajouter `kiyose_enqueue_admin_meta_box_assets()` sur `admin_enqueue_scripts` | Charge CSS/JS seulement sur les écrans d'édition pertinents |
| Données JS | Utiliser `wp_localize_script()` ou `wp_add_inline_script()` pour les chaînes traduites | Évite de générer des morceaux JS dans le PHP |

## Spécifications

### 1. Enqueue admin dédié

Créer `kiyose_enqueue_admin_meta_box_assets( string $hook_suffix ): void`.

Le chargement doit être limité à :

- `post.php`
- `post-new.php`

Et aux post types :

- `page`
- `kiyose_testimony` uniquement si nécessaire plus tard.

Le script doit dépendre de :

- `jquery`
- `media-editor`
- `wp-i18n` si les traductions sont gérées côté JS.

Appeler `wp_enqueue_media()` lorsque l'écran courant est `page`.

### 2. Extraire les styles inline

Créer `latelierkiyose/assets/css/admin/meta-boxes.css` avec les styles correspondant aux classes existantes :

- `.kiyose-meta-box`
- `.kiyose-meta-box__notice`
- `.kiyose-meta-box__fields`
- `.kiyose-meta-box__field-group`
- `.kiyose-meta-box__image-preview`
- `.kiyose-repeater-row`
- `.kiyose-repeater-row--qa`

Remplacer les attributs `style=""` dans le HTML par ces classes dès que possible.

### 3. Extraire les scripts inline

Créer `latelierkiyose/assets/js/admin/meta-boxes.js`.

Le script doit gérer :

- affichage/masquage des champs selon `#page_template` ;
- repeater mots-clefs ;
- repeater Q&R ;
- upload/suppression image hero ;
- upload/suppression photo contact ;
- sérialisation JSON avant soumission du formulaire `#post`.

Les sélecteurs existants peuvent être conservés pour limiter le changement :

- `#kiyose_welcome_keywords`
- `#kiyose_welcome_keywords_list`
- `#kiyose_content1_qa`
- `#kiyose_content1_qa_list`
- `#kiyose_hero_image_id`
- `#kiyose_contact_photo_id`

### 4. Extraire des helpers de rendu PHP

Ajouter des helpers dans `inc/meta-boxes.php` :

- `kiyose_render_template_meta_notice( bool $is_visible, string $template_label ): void`
- `kiyose_render_text_field( array $args ): void`
- `kiyose_render_textarea_field( array $args ): void`
- `kiyose_render_media_field( array $args ): void`
- `kiyose_render_meta_box_section_title( string $title, string $description = '' ): void`

Les helpers doivent échapper systématiquement labels, descriptions, IDs, valeurs et URLs.

### 5. Conserver la sauvegarde sécurisée

Les handlers actuels doivent conserver :

- vérification nonce ;
- rejet autosave ;
- `current_user_can( 'edit_post', $post_id )` ;
- restriction au template concerné ;
- sanitization par type ;
- `wp_slash( wp_json_encode() )` pour les JSON meta.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/inc/meta-boxes.php` | Extraire helpers, supprimer CSS/JS inline, garder save handlers |
| `latelierkiyose/assets/css/admin/meta-boxes.css` | Nouveau CSS admin |
| `latelierkiyose/assets/css/admin/meta-boxes.min.css` | Générer si les assets minifiés restent trackés |
| `latelierkiyose/assets/js/admin/meta-boxes.js` | Nouveau JS admin |
| `latelierkiyose/assets/js/admin/meta-boxes.min.js` | Générer si les assets minifiés restent trackés |
| `latelierkiyose/tests/Test_Meta_Boxes.php` | Étendre les tests de sanitization/helpers |

## Sécurité

- Ne pas supprimer les nonces.
- Ne pas assouplir les capabilities.
- Ne pas injecter de valeurs PHP non échappées dans des attributs HTML ou du JavaScript.
- Vérifier que les URLs restent passées par `esc_url_raw()` à la sauvegarde et `esc_url()` au rendu.

## Hors périmètre

- Migrer les meta boxes vers Gutenberg blocks ou ACF.
- Changer les noms des meta keys.
- Modifier l'expérience éditoriale ou les libellés fonctionnels.

## Tests et validation

### Tests automatisés

- [ ] Les tests existants de `kiyose_sanitize_welcome_keyword_item()` restent verts.
- [ ] Les tests existants de `kiyose_sanitize_qa_items()` restent verts.
- [ ] Ajouter un test pour JSON invalide dans les champs repeaters.
- [ ] Ajouter un test pour les helpers de rendu qui vérifie l'échappement d'une valeur HTML.
- [ ] `npm run lint:js` passe sur `assets/js/admin/meta-boxes.js`.
- [ ] `npm run lint:css` passe sur `assets/css/admin/meta-boxes.css`.

### Validation manuelle

- [ ] Sur une page sans template home, la meta box home affiche seulement la notice.
- [ ] Sur le template home, les champs home sont visibles.
- [ ] Ajouter/supprimer un mot-clef fonctionne et persiste.
- [ ] Ajouter/supprimer une Q&R fonctionne et persiste.
- [ ] Choisir/supprimer les images hero et contact fonctionne.
- [ ] Aucune erreur console dans l'éditeur.
