# Fonctionnalités

## 1. Page d'accueil (Homepage)

**Template**: `templates/page-home.php`

**Sections:**
- **Hero** - Présentation de Virginie et de l'atelier avec CTA principal
- **Les 4 Piliers** - Cartes cliquables vers chaque service (Art-thérapie, Rigologie, Bols tibétains, Philo)
- **Prochaines dates** - Widget Events Manager avec 3-4 prochains événements
- **Témoignages** - Carousel ou grille de 3 témoignages récents
- **Newsletter** - Formulaire d'inscription Brevo (email, prénom, nom optionnel)
- **Actualités** - 3 derniers articles du blog

## 2. Pages Services (4 piliers)

**Templates**: Pages individuelles avec `page.php` ou template custom par service

**Structure commune:**
- Titre du service avec sous-titre descriptif
- Description détaillée (plusieurs paragraphes)
- Bénéfices / Approche / Méthodologie
- Formats et durées proposés
- Tarifs indicatifs
- CTA vers contact/calendrier
- Galerie photos (optionnel)

**Services à créer:**
1. **Art-thérapie** - Programmes spécialisés (Burn-out, Deuil, "Oser être soi", Enfant intérieur)
2. **Rigologie/Yoga du rire** - Include "Silence Kiyose" (expérience casques audio)
3. **Bols tibétains** - Journées sonores + massage individuel
4. **Ateliers Philosophie** - Format débat + variante "Philo-Art"

## 3. Calendrier & Tarifs

**Template**: `templates/page-calendar.php`

**Contenu:**
- Intégration **Events Manager** (plugin déjà utilisé)
- Affichage des événements avec:
  - Date et heure
  - Type d'atelier
  - Lieu (Brux / Poitiers / En ligne)
  - Tarif
  - Places disponibles
  - Bouton "Réserver"
- **Note importante**: Pas de paiement en ligne, réservation seulement

## 4. Blog / Actualités

**Templates**: `archive.php`, `single.php`

**Fonctionnalités:**
- Archive des articles avec pagination
- Catégories (Conseils, Annonces, Réflexions, etc.)
- Article unique avec date de publication
- Partage sur réseaux sociaux
- Commentaires désactivés (`remove_post_type_support( 'post', 'comments' )` dans `setup.php`)

## 5. Témoignages

**Template**: `page.php` avec template part `content-testimony.php`

**Affichage:**
- Cartes de citations
- Attribution (prénom, contexte: individu/entreprise/structure)

**Implémentation**: Custom Post Type `kiyose_testimony` (déclaré dans `inc/custom-post-types.php`)
- Champs: citation (contenu), prénom, contexte (individu/entreprise/structure)
- Pas d'archive publique dédiée (affichés via template parts sur homepage et page témoignages)
- Query custom dans les templates pour afficher les témoignages

## 6. À Propos

**Template**: `templates/page-about.php`

**Contenu:**
- Biographie de Virginie Le Mignon
- Philosophie Kintsugi (métaphore importante)
- Parcours professionnel (20 ans d'expérience)
- Diplômes et certifications
- Photo(s) de Virginie

## 7. Contact

**Template**: `templates/page-contact.php`

**Éléments:**
- Formulaire de contact avec validation
  - Nom (requis)
  - Email (requis)
  - Message (requis)
  - Protection reCAPTCHA ou honeypot
- Informations de contact:
  - Cabinet principal: Le Grand Vron, 86510 Brux
  - Ateliers également proposés à Poitiers
  - Séances en ligne disponibles
  - Téléphone: 06 58 37 32 05
  - SIRET: 49219620900026
- Liens réseaux sociaux
- Carte Google Maps (optionnel)

## 8. Newsletter

**Emplacement**: Footer + Homepage

**Champs:**
- Email (requis)
- Prénom (requis)
- Nom (optionnel)
- Intégration avec **Brevo** (plugin déjà utilisé)
- Conformité RGPD

## 9. Navigation & Footer

**Navigation principale:**
- Utiliser `wp_nav_menu()` pour navigation dynamique
- Emplacement de menu: `primary` (à déclarer dans `functions.php`)
- Support de 2 niveaux:
  - **Niveau 1**: Items visibles dans la barre de navigation
  - **Niveau 2**: Items apparaissant au survol (dropdown menu)
- Tous les items sont cliquables (pas de séparateurs non-cliquables)
- Configuration via WordPress Admin: `/wp-admin/nav-menus.php`
- Accessible au clavier (WCAG 2.2 AA)

**Menu mobile (hamburger):**
- Bouton hamburger avec `aria-expanded="false/true"` et `aria-controls="mobile-menu"`
- Ouverture: overlay plein écran ou slide-in depuis la droite
- Focus trap activé quand le menu est ouvert (Tab circule uniquement dans le menu)
- `body` scroll verrouillé quand le menu est ouvert (`overflow: hidden`)
- Fermeture: bouton close, touche Esc, ou clic sur overlay
- Retour du focus sur le bouton hamburger à la fermeture
- Transition: 200-300ms, respecte `prefers-reduced-motion`

**Footer:**
- Liens réseaux sociaux (Facebook, Instagram, LinkedIn @latelierkiyose)
- Newsletter Brevo
- Mentions légales / Politique de confidentialité
- Copyright
- SIRET

## 10. Page 404

**Template**: `404.php`

**Contenu:**
- Message clair indiquant que la page n'existe pas
- Lien vers la page d'accueil
- Formulaire de recherche
- Suggestion de pages populaires (services, calendrier)
- Design cohérent avec le reste du site (pas une page "technique")

## 11. Recherche

**Template**: `search.php`

**Contenu:**
- Formulaire de recherche avec champ et bouton accessible
- Liste des résultats avec titre, extrait, date
- Pagination des résultats
- Message explicite quand aucun résultat n'est trouvé
- Réutilise `template-parts/content-blog.php` pour l'affichage des résultats

## 12. Mentions légales & Politique de confidentialité

**Template**: `page.php` (pages WordPress standard)

**Contenu obligatoire (loi LCEN, France):**
- Identité: Virginie Le Mignon, L'atelier Kiyose
- SIRET: 49219620900026
- Adresse: Le Grand Vron, 86510 Brux
- Hébergeur: nom, adresse, contact
- Responsable de publication
- Politique de confidentialité (RGPD): données collectées, finalités, durée de conservation, droits des utilisateurs
- Politique cookies (liée au plugin Complianz)
