# PRD 0037 — Photo de contact

## Statut

**terminé**

## Objectif

Ajouter une photo optionnelle (portrait) en haut du cadre d'informations de la page de contact, pour humaniser le premier contact.

## Contexte

La page de contact (`templates/page-contact.php`) affiche un layout deux colonnes : formulaire (2fr) + informations de contact (1fr). Le cadre `<aside class="contact-page__info">` contient actuellement un titre « Coordonnées » et les items de contact (téléphone, email, adresse, réseaux sociaux). Il n'y a aucune image.

**État actuel de `.contact-page__info` :**

- Fond blanc, `border-radius: 12px`, ombre légère
- Padding : `--kiyose-spacing-xl` (mobile), `--kiyose-spacing-2xl` (tablette+)
- Sticky en desktop (`position: sticky; top: --kiyose-spacing-xl`)
- Pas de meta box existante pour la page contact

**Précédents dans le thème :**

- Meta box image : `kiyose_hero_image_id` dans `inc/meta-boxes.php` (home page)
- Photo ronde : `.about-overlay__image` (120px) et `.home-about__image` (200px) dans `about-overlay.css`

## Spécifications

### 1. Meta box — `inc/meta-boxes.php`

Ajouter une meta box dédiée à la page de contact, affichée uniquement quand le template `page-contact.php` est actif. Même pattern que la meta box home hero existante.

**Champs :**

| Champ            | Type                   | Clé post_meta              | Description                   |
| ---------------- | ---------------------- | -------------------------- | ----------------------------- |
| Photo            | Image (media uploader) | `kiyose_contact_photo_id`  | ID de l'image sélectionnée    |
| Texte alternatif | Texte court            | `kiyose_contact_photo_alt` | Alt text pour l'accessibilité |

**Rendu admin :**

```php
<fieldset>
    <legend>Photo de contact</legend>
    <!-- Champ image : bouton "Choisir une image" / "Supprimer", preview -->
    <!-- Champ alt : <input type="text"> -->
</fieldset>
```

**Sécurité :**

- Nonce : `kiyose_contact_photo_nonce` / action `kiyose_save_contact_photo`
- Sanitization : `absint()` pour l'ID image, `sanitize_text_field()` pour l'alt
- Vérification `current_user_can( 'edit_post', $post_id )`

**JavaScript media uploader :**

Réutiliser le même pattern que le hero image (bouton select, bouton remove, preview). Le JS existant dans `meta-boxes.php` gère déjà le pattern `wp.media()` ; étendre ou dupliquer pour le nouveau champ.

### 2. Template — `templates/page-contact.php`

Ajouter la photo **avant** le `<h2>Coordonnées</h2>`, à l'intérieur de `<aside class="contact-page__info">`.

```php
<aside class="contact-page__info" aria-label="Informations de contact">
    <?php
    $kiyose_contact_photo_id  = get_post_meta( get_the_ID(), 'kiyose_contact_photo_id', true );
    $kiyose_contact_photo_alt = get_post_meta( get_the_ID(), 'kiyose_contact_photo_alt', true );
    if ( ! empty( $kiyose_contact_photo_id ) ) :
    ?>
        <div class="contact-page__photo">
            <?php
            echo wp_get_attachment_image(
                $kiyose_contact_photo_id,
                'medium',
                false,
                array( 'alt' => esc_attr( $kiyose_contact_photo_alt ) )
            );
            ?>
        </div>
    <?php endif; ?>
    <h2>Coordonnées</h2>
    <!-- ... reste inchangé ... -->
</aside>
```

**Taille WordPress** : `medium` (300×300 par défaut). Suffisant pour une image affichée en cercle avec max-width de 200px. Pas besoin d'un `add_image_size()` dédié.

### 3. CSS — `assets/css/components/contact-page.css`

```css
.contact-page__photo {
	aspect-ratio: 1 / 1;
	border-radius: 50%;
	box-shadow: 0 4px 12px rgb(93 5 5 / 12%);
	margin: 0 auto var(--kiyose-spacing-xl);
	max-width: 200px;
	overflow: hidden;
	width: 100%;
}

.contact-page__photo img {
	height: 100%;
	object-fit: cover;
	width: 100%;
}
```

**Comportement responsive :**

| Breakpoint         | Comportement                                                                     |
| ------------------ | -------------------------------------------------------------------------------- |
| Mobile (< 768px)   | Cercle de max 200px, centré dans le cadre pleine largeur                         |
| Tablette (≥ 768px) | Idem, centré                                                                     |
| Desktop (≥ 1024px) | Cercle contraint par la largeur du sidebar (1fr dans le grid 2fr/1fr), max 200px |

La `max-width: 200px` empêche l'image d'être trop grande sur mobile (où `.contact-page__info` occupe toute la largeur).

### 4. Minification

Régénérer `contact-page.min.css` après modification.

## Périmètre

**Inclus :**

- Meta box avec sélecteur d'image + champ alt text
- Affichage conditionnel dans le template contact
- CSS de la photo ronde

**Hors périmètre :**

- Pas de nouveau `add_image_size()` (taille `medium` suffit)
- Pas de modification du layout du cadre info (la photo s'insère au-dessus du contenu existant)
- Pas de lazy loading explicite (`wp_get_attachment_image` ajoute `loading="lazy"` par défaut depuis WP 5.5)

## Accessibilité

| Critère                 | Spécification                                                                                                                 |
| ----------------------- | ----------------------------------------------------------------------------------------------------------------------------- |
| Alt text                | Champ dédié dans la meta box, rendu via `alt="..."` sur `<img>`                                                               |
| Image absente           | Si pas de photo configurée, aucun markup rendu (pas de conteneur vide)                                                        |
| Décoratif vs informatif | Si l'admin laisse l'alt vide, `alt=""` sera rendu (image décorative). Recommandation admin : renseigner le nom de la personne |
| CLS                     | L'`aspect-ratio: 1 / 1` + dimensions explicites évitent le layout shift                                                       |

## Fichiers à modifier

| Fichier                                      | Modification                                                                         |
| -------------------------------------------- | ------------------------------------------------------------------------------------ |
| `inc/meta-boxes.php`                         | Nouvelle meta box `kiyose_contact_photo` (register, render, save, JS media uploader) |
| `templates/page-contact.php`                 | Ajout du bloc `.contact-page__photo` conditionnel avant `<h2>`                       |
| `assets/css/components/contact-page.css`     | Styles `.contact-page__photo` et `.contact-page__photo img`                          |
| `assets/css/components/contact-page.min.css` | Régénérer                                                                            |

## Tests et validation

### Visuel

- [ ] Photo ronde, centrée horizontalement dans le cadre info
- [ ] Photo en haut du cadre, avant le titre « Coordonnées »
- [ ] Ombre portée subtile cohérente avec les autres images rondes du thème
- [ ] Pas de déformation de l'image (crop centré via `object-fit: cover`)
- [ ] Responsive correct à 320px, 768px, 1024px, 1280px
- [ ] Le cadre `.contact-page__info` ne change pas de largeur avec/sans photo
- [ ] Sans photo configurée : aucun espace vide, le titre est en haut du cadre

### Admin

- [ ] Meta box visible uniquement sur les pages utilisant le template « Page de contact »
- [ ] Bouton « Choisir une image » ouvre le media uploader
- [ ] Preview de l'image affichée dans la meta box après sélection
- [ ] Bouton « Supprimer » retire l'image et masque la preview
- [ ] Champ alt text sauvegardé et restauré correctement
- [ ] Sécurité : nonce vérifié, permissions vérifiées, inputs sanitized

### Accessibilité

- [ ] Alt text rendu sur l'`<img>` si renseigné
- [ ] `alt=""` si le champ alt est vide (image décorative)
- [ ] Pas de CLS (Cumulative Layout Shift) au chargement de l'image

### Standards

- [ ] `make test` passe (PHPCS, Stylelint)
- [ ] Variables CSS préfixées `--kiyose-`
- [ ] Classes BEM : `contact-page__photo`
- [ ] Fonctions PHP préfixées `kiyose_`
