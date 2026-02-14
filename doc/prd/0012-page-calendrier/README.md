# PRD 0012 — Calendrier & tarifs

## Statut

**brouillon**

## Objectif

Créer le template de la page "Calendrier & Tarifs" avec l'intégration d'Events Manager pour l'affichage des événements et réservations.

## Dépendances

- **PRD 0005** : Templates de base.

## Pré-requis

- **Events Manager** : Plugin déjà installé et configuré sur le site existant. Les événements sont déjà créés.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── templates/
│   └── page-calendar.php              # Template calendrier & tarifs
└── assets/
    └── css/
        └── components/
            └── events-manager.css     # Override styles Events Manager
```

## Spécifications détaillées

### `templates/page-calendar.php`

**Header du fichier** :
```php
<?php
/*
Template Name: Calendrier et tarifs
*/
```

**Structure** :
```
get_header()

<article class="calendar-page">
    <header class="calendar-page__header">
        <h1 class="calendar-page__title"><?php the_title(); ?></h1>
        <p class="calendar-page__intro"><!-- Texte d'introduction --></p>
    </header>

    <div class="calendar-page__content">
        <?php the_content(); ?>
        <!-- Le contenu contient les shortcodes Events Manager -->
    </div>
</article>

get_footer()
```

**Points clés** :
- Le template est simple : il affiche `the_content()` qui contiendra les shortcodes Events Manager
- Les shortcodes Events Manager existants (liste d'événements, calendrier) sont placés dans l'éditeur WordPress
- Le travail principal est le styling CSS des éléments générés par Events Manager

### Styling Events Manager

Events Manager génère son propre markup HTML. Les styles doivent être overridés pour matcher le design system du thème.

**Éléments Events Manager à styler** :

1. **Liste d'événements** :
   - `.em-events-list` — Conteneur de la liste
   - Chaque événement : date, titre, lieu, tarif, places disponibles
   - Bouton "Réserver" stylé comme `.button--primary`

2. **Calendrier** (si utilisé) :
   - `.em-calendar` — Tableau calendrier
   - Couleurs de la palette pour les jours avec événements
   - Navigation mois précédent/suivant

3. **Détail événement** (si affiché inline) :
   - Titre, date/heure, lieu, description, tarif
   - Bouton de réservation

**Informations à afficher par événement** :
- Date et heure
- Type d'atelier (si catégorisé dans Events Manager)
- Lieu (Brux / Poitiers / En ligne)
- Tarif
- Places disponibles
- Bouton "Réserver" (lien vers le formulaire de réservation Events Manager)

**Note** : Pas de paiement en ligne. Le bouton "Réserver" mène au formulaire de réservation d'Events Manager (inscription sans paiement).

### Approche CSS

L'override des styles Events Manager se fait en augmentant la spécificité CSS :

```css
.calendar-page .em-events-list { ... }
.calendar-page .em-item { ... }
```

Ou via le conteneur `.calendar-page__content` pour scoper les styles.

Points de style importants :
- Cartes d'événements avec le style `.card` du design system
- Typographie cohérente (Lora pour titres, Nunito pour corps)
- Couleurs de la palette
- Boutons conformes au design system
- Touch targets ≥ 44x44px

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur les fichiers PHP créés

### Tests manuels
- [ ] Le template "Calendrier et tarifs" apparaît dans le menu des templates
- [ ] Les événements Events Manager s'affichent avec le bon styling
- [ ] Les boutons de réservation sont fonctionnels
- [ ] La navigation du calendrier fonctionne (si calendrier utilisé)

### Tests manuels — Accessibilité
- [ ] Un seul `<h1>`
- [ ] Les événements sont navigables au clavier
- [ ] Les boutons de réservation ont un focus visible
- [ ] Le tableau calendrier (si utilisé) est accessible avec les attributs appropriés
- [ ] Contrastes respectés sur les éléments Events Manager

### Tests manuels — Responsive
- [ ] Liste d'événements lisible sur mobile
- [ ] Calendrier (si utilisé) adapté ou remplacé par liste sur mobile
- [ ] Boutons de réservation accessibles sur mobile (touch targets)

## Hors périmètre

- Configuration d'Events Manager (le plugin est déjà configuré)
- Création d'événements (faite dans l'admin Events Manager)
- Paiement en ligne (pas prévu)
- Section "Prochaines dates" de la homepage (PRD 0008 — partage le CSS)
