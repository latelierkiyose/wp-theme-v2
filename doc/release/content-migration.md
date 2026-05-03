# Migration du contenu existant

Ce document décrit toutes les actions à réaliser sur le contenu existant du site une fois le nouveau thème activé. Le site actuel a déjà des pages et des médias — il faut les reconnecter aux nouveaux templates, metadata, CPT et shortcodes.

> 🔗 Revenir à la procédure principale → [`README.md`](README.md)

## Vue d'ensemble

| # | Action | Durée approx. | Bloquant si omis ? |
|---|---|---|---|
| 1 | Assigner un template à chaque page | 10 min | ✅ oui (sinon rendu par défaut non stylé) |
| 2 | Configurer Réglages → Lecture | 5 min | ✅ oui (home/blog peuvent utiliser le mauvais template) |
| 3 | Configurer les boutons des pages de service | 5 min | ✅ oui (sinon CTAs masqués) |
| 4 | Configurer les liens légaux du footer | 5 min | ✅ oui (sinon liens légaux absents) |
| 5 | Remplir les métadonnées de la page Accueil | 20 min | ⚠️ partiellement (sections vides) |
| 6 | Remplir les métadonnées de la page Contact | 5 min | non |
| 7 | Configurer les catégories calendrier des services | 5 min/page | non (vide = toutes les dates) |
| 8 | Recréer les témoignages en CPT `kiyose_testimony` | 15-30 min | ⚠️ sinon carrousel vide |
| 9 | Remplacer les anciens blocs « signets » | 10 min/page | non (rendu dégradé) |
| 10 | Insérer les shortcodes des plugins tiers | 10 min | ✅ oui (formulaires cassés) |
| 11 | Régénérer les miniatures | 5 min (+ attente) | non (images coupées sinon) |

---

## 1. Assigner un template à chaque page

Dans l'admin WordPress, pour chaque page ci-dessous : **Pages → (page) → sidebar droite → Attributs de page → Modèle**, sélectionner le template correspondant puis **Mettre à jour**.

| Page existante (titre) | Slug attendu | Template à sélectionner | Fichier de référence |
|---|---|---|---|
| Accueil | `/` (page d'accueil statique) | **Page d'accueil** | `latelierkiyose/templates/page-home.php` |
| À propos | `/a-propos/` | **À propos** | `latelierkiyose/templates/page-about.php` |
| Contact | `/contact/` | **Page de contact** | `latelierkiyose/templates/page-contact.php` |
| Calendrier & Tarifs | `/calendrier/` ou slug existant | **Calendrier et tarifs** | `latelierkiyose/templates/page-calendar.php` |
| Art-thérapie | `/art-therapie/` (ou équivalent) | **Page de service** | `latelierkiyose/templates/page-services.php` |
| Rigologie / Yoga du rire | `/rigologie/` | **Page de service** | `latelierkiyose/templates/page-services.php` |
| Bols tibétains | `/bols-tibetains/` | **Page de service** | `latelierkiyose/templates/page-services.php` |
| Ateliers philosophie | `/ateliers-philo/` | **Page de service** | `latelierkiyose/templates/page-services.php` |

Les boutons des pages de service ne dépendent plus d'un slug précis. Ils utilisent les pages choisies dans le Customizer (voir § 3).

**Vérification** : après l'assignment, afficher chaque page sur le front et constater que la mise en page correspond au design attendu.

---

## 2. Configurer Réglages → Lecture

Dans l'admin WordPress, ouvrir **Réglages → Lecture** :

1. Sélectionner **Une page statique**.
2. **Page d'accueil** : choisir la page `Accueil`.
3. **Page des articles** : choisir la page `Actus / Blog` ou `Actualités`.
4. Enregistrer.

**Vérification** : afficher `/actus-blog/`. La page doit utiliser le layout Actualités / Blog avec le titre `Actus / Blog`, la liste d'articles et la colonne Recherche / Catégories.

---

## 3. Configurer les boutons des pages de service

Dans l'admin WordPress :

1. Ouvrir **Apparence → Personnaliser → Pages de service — boutons**.
2. **Page du bouton « Me contacter »** : choisir la page Contact publiée.
3. **Page du bouton « Voir le calendrier »** : choisir la page Calendrier & Tarifs publiée.
4. Publier les réglages du Customizer.

Si une page cible n'est pas configurée ou n'est pas publiée, le bouton correspondant est masqué sur les pages de service. Il ne faut pas modifier le template pour changer ces liens.

**Vérification** : ouvrir une page de service sur le front. Les boutons doivent pointer vers les pages publiées choisies, même si le slug de la page calendrier change.

---

## 4. Configurer les liens légaux du footer

Dans l'admin WordPress :

1. Ouvrir **Apparence → Personnaliser → Footer — liens légaux**.
2. **Page « Mentions légales »** : choisir la page publiée des mentions légales.
3. **Page « Politique de confidentialité »** : choisir la page publiée de politique de confidentialité.
4. Publier les réglages du Customizer.
5. Vérifier sur le front que les deux liens du footer pointent vers les pages choisies.

Si une page cible n'est pas configurée ou n'est pas publiée, le lien correspondant est masqué dans le footer. Il ne faut pas modifier le template ni dépendre d'un slug précis pour changer ces liens.

**Vérification** : ouvrir le footer sur le front et confirmer que les deux liens ne retournent pas de 404.

---

## 5. Métadonnées de la page Accueil

Le template `page-home.php` lit ses contenus éditoriaux depuis des **meta fields** gérés dans une meta box dédiée (« Page d'accueil — Bienvenue & Overlay À propos »). Cette meta box n'apparaît qu'après avoir sélectionné le template « Page d'accueil », enregistré, puis rechargé l'éditeur si nécessaire.

Source de vérité : `latelierkiyose/inc/meta-boxes.php`.

Ouvrir la page Accueil en édition. Après sauvegarde du template, la meta box apparaît **sous l'éditeur de contenu principal**. Remplir les champs suivants :

### Bloc Bienvenue

| Champ admin | Clé meta | Type | Obligatoire |
|---|---|---|---|
| Titre (H1) du bloc bienvenue | `kiyose_welcome_title` | Texte court | ✅ |
| Sous-titre | `kiyose_welcome_subtitle` | Texte court | recommandé |
| Texte descriptif | `kiyose_welcome_text` | Textarea | recommandé |
| Mots-clefs (chips cliquables) | `kiyose_welcome_keywords` | Repeater (label + URL) | recommandé |
| Slogan | `kiyose_welcome_slogan` | Texte court, typo Dancing Script | recommandé |

### Overlay « À propos » (panneau flottant au premier scroll)

| Champ admin | Clé meta | Défaut si vide |
|---|---|---|
| Texte de description | `kiyose_hero_content` | Texte par défaut rassurant (voir code) |
| Texte du lien « En savoir plus » | `kiyose_hero_cta_text` | `En savoir plus sur l'atelier` |
| URL de la page « À propos » | `kiyose_hero_cta_url` | `/a-propos/` |
| Photo (overlay / mobile) | `kiyose_hero_image_id` | aucune (image masquée) |

### Bloc Contenu 1 — Questions & Réponses

| Champ admin | Clé meta | Type |
|---|---|---|
| Repeater Q&R | `kiyose_content1_qa` | Liste d'objets `{question, answer}` |
| Citation / Slogan | `kiyose_content1_slogan` | Texte court |

Si le repeater Q&R est vide, la section entière est masquée.

### Bloc Contenu 2 — Texte libre

| Champ admin | Clé meta | Type |
|---|---|---|
| Texte riche (gras, italique, listes, liens) | `kiyose_content2_text` | HTML limité (`wp_kses_post`) |
| Citation (sans guillemets) | `kiyose_content2_slogan` | Texte court |
| Auteurice de la citation | `kiyose_content2_quote_author` | Texte court |

Action de migration manuelle si une ancienne citation contient déjà les guillemets et l'auteurice dans `kiyose_content2_slogan` :

1. Retirer les guillemets du champ **Citation (sans guillemets)**.
2. Déplacer le nom de l'auteurice dans le champ **Auteurice de la citation**.

Exemple : `« Heureux soient les fêlés, car ils laissent passer la lumière » Michel Audiard` devient :

| Champ | Valeur |
|---|---|
| Citation (sans guillemets) | `Heureux soient les fêlés, car ils laissent passer la lumière` |
| Auteurice de la citation | `Michel Audiard` |

**Recommandation** : reprendre les textes de la homepage actuelle et les répartir entre les champs bienvenue / overlay / Q&R / contenu libre selon le design cible. Ne rien coller dans l'éditeur de contenu principal de la page — sur ce template il n'est pas rendu.

---

## 6. Métadonnées de la page Contact

Source de vérité : `latelierkiyose/inc/meta-boxes.php`.

Une fois le template « Page de contact » assigné, enregistré, puis l'éditeur rechargé si nécessaire, la meta box « Page de contact — Photo » devient active :

| Champ admin | Clé meta | Obligatoire |
|---|---|---|
| Photo de contact | `kiyose_contact_photo_id` | non |
| Texte alternatif (accessibilité) | `kiyose_contact_photo_alt` | recommandé si photo présente |

**Bonnes pratiques** :
- Format carré recommandé (l'aside affiche l'image en bandeau)
- Texte alt : nom de la personne (ex. « Virginie Dericbourg ») — laisser vide uniquement si l'image est purement décorative.

Les coordonnées (téléphone, email, adresse, réseaux sociaux) sont **hardcodées** dans `latelierkiyose/templates/page-contact.php:54-101`. Toute mise à jour nécessite une intervention développeur. Valeurs actuelles à vérifier avant déploiement :

- Téléphone : `06 58 37 32 05`
- Email : `contact@latelierkiyose.fr`
- Adresse : `Le Grand Vron, 86510 Brux, Nouvelle-Aquitaine`
- Réseaux : Facebook / Instagram / LinkedIn `@latelierkiyose`

---

## 7. Catégories calendrier des pages de service

Chaque page de service peut filtrer le calendrier sur une ou plusieurs catégories Events Manager.

Pour chaque page utilisant le template **Page de service** :

1. Ouvrir la page en édition.
2. Vérifier que le template **Page de service** est bien sélectionné.
3. Enregistrer puis recharger l'éditeur si la meta box **Page de service — Calendrier** n'apparaît pas.
4. Cocher les catégories Events Manager à afficher après clic sur **Voir le calendrier**.
5. Laisser toutes les cases décochées si cette page doit afficher toutes les dates.
6. Mettre à jour.

Exemples :

| Page de service | Catégories à cocher |
|---|---|
| Art-thérapie | `art-therapie` |
| Ateliers philosophie | `ateliers-philo`, ou aucune si toutes les dates doivent rester visibles |

**Vérification** : depuis une page avec catégories cochées, le bouton calendrier ajoute un paramètre `kiyose_event_categories` dans l'URL. Depuis une page sans catégorie cochée, le bouton pointe vers le calendrier sans paramètre.

---

## 8. Témoignages — migration vers le CPT `kiyose_testimony`

Source de vérité : `latelierkiyose/inc/custom-post-types.php:14-58`.

Le thème remplace l'ancien système de témoignages (peu importe lequel) par un Custom Post Type dédié : `kiyose_testimony`, non public (pas de page single), consommé uniquement via le shortcode `[kiyose_testimonials]`.

### Processus de migration

1. **Avant l'activation du thème** : exporter tous les témoignages existants dans un document texte (auteur, contenu, contexte).
2. **Après l'activation** : dans l'admin, un menu « Témoignages » apparaît. Pour chaque témoignage :
   - Témoignages → Ajouter
   - **Titre** : nom de l'auteur (ex. « Sophie D. »)
   - **Contenu** : texte du témoignage
   - **Contexte** (meta box dédiée, champ `kiyose_testimony_context`) : choisir entre
     - `individual` — témoignage de particulier
     - `enterprise` — témoignage de dirigeant / RH
     - `structure` — témoignage d'institution (école, association…)
   - Publier
3. **Où les témoignages s'affichent** :
   - **Page d'accueil** : le partial `template-parts/home/testimonials.php` interroge directement le CPT `kiyose_testimony` et rend le carrousel automatiquement. Aucun shortcode à insérer.
   - **Autre page** : pour afficher des témoignages ailleurs, utiliser le shortcode `[kiyose_testimonials]` :
     - Carrousel : `[kiyose_testimonials display="carousel" limit="6"]`
     - Grille : `[kiyose_testimonials display="grid" columns="3" limit="9"]`
     - Filtrage par contexte : `[kiyose_testimonials context="enterprise"]`

Documentation rédacteur complète : [`doc/user/05-temoignages.md`](../user/05-temoignages.md).

### Ce qu'il ne faut pas faire

- Ne pas installer un plugin de témoignages tiers — voir [`plugins.md`](plugins.md), section « Plugins à NE PAS installer ».
- Ne pas utiliser le contenu de `<blockquote>` pour les témoignages : le shortcode alimente aussi le carrousel avec effets visuels cohérents.

---

## 9. Migration des signets de navigation intra-page

Certaines pages actuelles (principalement les pages de service) utilisent un bloc « Colonnes » avec la classe `signets` pour créer un sommaire cliquable en haut de page :

```html
<div class="wp-block-columns signets">
  <div class="wp-block-column"><a href="#mademarche">Ma Démarche</a></div>
  <div class="wp-block-column"><a href="#mesoutils">Mes Outils</a></div>
  ...
</div>
```

Le nouveau thème fournit un shortcode sémantique, accessible et responsive : `[kiyose_signets]`.

### Migration en mode auto-scan (recommandé)

Le thème ajoute **automatiquement** des IDs aux H2 qui n'en ont pas (source : `latelierkiyose/inc/content-filters.php`). Donc dans la plupart des cas, aucune modification des H2 n'est nécessaire.

1. Éditer la page (ex. Art-thérapie)
2. Supprimer le bloc `<div class="wp-block-columns signets">...</div>` existant
3. Le remplacer par le shortcode vide :
   ```
   [kiyose_signets][/kiyose_signets]
   ```
4. Mettre à jour
5. Tester la navigation : chaque lien du sommaire doit scroller vers son H2

### Migration en mode manuel (si besoin de contrôler les entrées)

Si seulement une partie des H2 doit apparaître dans le sommaire, utiliser le format un lien par ligne :

```
[kiyose_signets]
Ma Démarche | #ma-demarche
Mes Outils | #mes-outils
Mes Ateliers | #mes-ateliers
[/kiyose_signets]
```

Format de chaque ligne : `Texte du lien | #ancre`. L'ancre doit correspondre à l'ID généré automatiquement (version slugifiée du titre H2, règle : minuscules + tirets + accents retirés, via `sanitize_title()`).

**Exemples d'IDs générés** :
- « Ma Démarche » → `#ma-demarche`
- « Mes Outils » → `#mes-outils`
- « Parcours thématiques » → `#parcours-thematiques`

Pour forcer un ID custom sur un H2 : Gutenberg → sélectionner le H2 → Sidebar droite → Avancé → Ancrage HTML, saisir l'ID sans le `#`.

Documentation rédacteur complète : [`doc/user/04-shortcodes.md`](../user/04-shortcodes.md).

---

## 10. Insertion des shortcodes des plugins tiers

### Page Contact — Contact Form 7

La page utilise `the_content()` ; insérer le shortcode CF7 dans le contenu principal :

1. Contact → Formulaires de contact → noter l'ID du formulaire existant (ex. `123`)
2. Éditer la page Contact → bloc « Shortcode » ou directement en ligne : `[contact-form-7 id="123" title="Contact"]`
3. Mettre à jour
4. Tester une soumission et vérifier la réception de l'email

### Page Calendrier & Tarifs — Events Manager

La page utilise `the_content()` ; insérer le shortcode calendrier du thème dans le contenu :

1. Éditer la page « Calendrier & Tarifs » → bloc « Shortcode » : `[kiyose_events_list]`
2. Ajouter au besoin du texte introductif, une grille de tarifs, un bloc FAQ — tout cela vit dans le contenu.
3. Mettre à jour
4. Vérifier sur le front : la liste d'événements doit s'afficher.

Ce shortcode relaie vers Events Manager avec les événements futurs et applique automatiquement les catégories reçues depuis les boutons des pages de service. Si la page utilise encore un shortcode Events Manager brut (`[events_list ...]`), les événements s'affichent, mais le filtrage par service ne s'applique pas.

### Pages détail des événements — Events Manager

Le template `single.php` est aussi utilisé pour les événements. Le thème remplace la navigation WordPress native par une navigation basée sur la date de début Events Manager.

1. Ouvrir au moins deux événements publiés avec des dates de début différentes.
2. Vérifier que les pages détail d'événement s'affichent sans erreur.
3. Vérifier que les liens « Événement précédent » et « Événement suivant », quand ils sont présents, suivent l'ordre chronologique des dates d'événement.
4. Si la navigation est absente sur un événement, vérifier que sa date de début est bien renseignée dans Events Manager.

### Homepage — le shortcode Events Manager est déjà inclus par le template

Rien à faire sur la home, le shortcode `[events_list limit="4" scope="future"...]` est rendu automatiquement par `template-parts/home/events.php`.

### Newsletter Brevo — automatique

Rien à faire. Le formulaire newsletter du footer est rendu automatiquement par le thème, à condition que le plugin Brevo soit actif et qu'un formulaire d'ID 1 existe. Sur la homepage desktop, ce même formulaire est déplacé dans l'overlay newsletter quand il apparaît, puis replacé dans le footer quand l'overlay se ferme. Le shortcode Brevo n'est rendu qu'une seule fois afin d'éviter les doublons reCAPTCHA. Voir [`plugins.md` § Brevo](plugins.md#1-brevo-newsletter).

---

## 11. Tailles d'images et régénération des miniatures

Le thème déclare deux tailles personnalisées (source : `latelierkiyose/inc/setup.php:46-47`) :

| Nom de la taille | Dimensions | Crop | Utilisation |
|---|---|---|---|
| `kiyose-hero` | 1920 × 800 px | oui | Image à la une des pages de service, bandeau header |
| `kiyose-testimony-thumb` | 150 × 150 px | oui | Avatars du carrousel de témoignages |

Les **images déjà présentes dans la médiathèque avant l'activation du thème n'ont pas ces tailles générées**. Pour les créer :

1. Installer temporairement le plugin **Regenerate Thumbnails**
2. Outils → Regenerate Thumbnails → lancer sur tous les attachments
3. Attendre la fin du traitement (peut prendre du temps selon le volume)
4. Désinstaller Regenerate Thumbnails

Alternative en ligne de commande (si WP-CLI disponible) :

```bash
wp media regenerate --yes
```

**Images à vérifier prioritairement** :
- Image à la une de chaque page de service (devrait avoir ≥ 1920 px de large pour le crop `kiyose-hero`)
- Photo de contact (`kiyose_contact_photo_id`)
- Image overlay de la homepage (`kiyose_hero_image_id`)
- Avatars des auteurs de témoignages (si chaque témoignage a une image à la une)

---

## Checklist migration contenu

Avant de considérer la migration comme terminée :

- [ ] Toutes les pages ont leur template assigné (voir tableau § 1)
- [ ] Réglages → Lecture : page d'accueil = `Accueil`, page des articles = `Actus / Blog` ou `Actualités`
- [ ] Customizer : pages cibles « Me contacter » et « Voir le calendrier » configurées
- [ ] Customizer : les deux liens légaux du footer sont configurés et ne retournent pas de 404
- [ ] Page Accueil : titre bienvenue, slogan, Q&R, textes contenu 2 remplis
- [ ] Page Accueil : overlay avec photo (optionnelle) et URL À propos correcte
- [ ] Page Contact : photo + alt remplis, formulaire CF7 inséré, envoi testé
- [ ] Page Calendrier : shortcode `[kiyose_events_list]` inséré et rend la liste sur le front
- [ ] Au moins un témoignage publié en CPT `kiyose_testimony` (la home rend automatiquement le carrousel via `WP_Query` sur ce CPT — pas besoin d'insérer `[kiyose_testimonials]` sur la home)
- [ ] Au moins 3 témoignages créés en CPT avec contexte
- [ ] Chaque page de service a son image à la une (`kiyose-hero`)
- [ ] Chaque page de service a ses catégories calendrier vérifiées (ou aucune catégorie si toutes les dates doivent s'afficher)
- [ ] Anciens blocs `wp-block-columns signets` remplacés par `[kiyose_signets]`
- [ ] Miniatures régénérées
- [ ] Les boutons des pages de service pointent vers les pages configurées et ne retournent pas de 404
