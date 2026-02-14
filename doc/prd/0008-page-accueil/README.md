# PRD 0008 — Page d'accueil

## Objectif

Créer le template de la page d'accueil avec toutes ses sections : hero, 4 piliers, prochaines dates, témoignages (carousel), newsletter, et actualités.

## Dépendances

- **PRD 0005** : Templates de base (layout page).
- **PRD 0006** : Blog (`content-blog.php` pour les actualités).
- **PRD 0007** : CPT Témoignages (`content-testimony.php` pour le carousel).

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── templates/
│   └── page-home.php                      # Template page d'accueil
├── template-parts/
│   └── content-service.php                # Carte de service (4 piliers)
├── assets/
│   ├── css/
│   │   └── components/
│   │       ├── hero.css                   # Styles hero
│   │       ├── service-card.css           # Styles cartes services
│   │       ├── carousel.css               # Styles carousel témoignages
│   │       ├── events-preview.css         # Styles prochains événements
│   │       └── home-sections.css          # Styles généraux sections homepage
│   └── js/
│       └── modules/
│           └── carousel.js                # Module carousel accessible
```

## Spécifications détaillées

### `templates/page-home.php`

**Header du fichier** :
```php
<?php
/*
Template Name: Page d'accueil
*/
```

**Sections dans l'ordre** :

#### 1. Hero

```html
<section class="hero-section" aria-label="Présentation de l'atelier">
    <div class="hero-section__content">
        <h1 class="hero-section__title"><!-- Tagline principale --></h1>
        <p class="hero-section__subtitle"><!-- Sous-titre descriptif --></p>
        <a href="/calendrier-tarifs/" class="button button--primary hero-section__cta">
            Découvrir les prochains ateliers
        </a>
    </div>
</section>
```

- Image de fond ou couleur de la palette
- Overlay vitrail subtil (voir `references/css-examples.css`)
- Le contenu textuel (tagline, sous-titre) est dans le template PHP — le contenu final sera ajusté par le client
- CTA principal pointant vers le calendrier

#### 2. Les 4 Piliers

```html
<section class="home-services" aria-labelledby="services-title">
    <h2 id="services-title">Nos activités</h2>
    <div class="home-services__grid">
        <!-- 4x template-parts/content-service.php -->
    </div>
</section>
```

- Grille de 4 cartes cliquables (1 colonne mobile, 2 tablet, 4 desktop)
- Chaque carte renvoie vers la page du service correspondant
- Les données des services (titre, description courte, image, lien) sont récupérées dynamiquement à partir de pages WordPress existantes, identifiées par slug ou champ custom

#### 3. Prochaines dates

```html
<section class="home-events" aria-labelledby="events-title">
    <h2 id="events-title">Prochaines dates</h2>
    <div class="home-events__list">
        <!-- Shortcode Events Manager ou widget -->
    </div>
    <a href="/calendrier-tarifs/" class="button button--secondary">
        Voir tout le calendrier
    </a>
</section>
```

- Afficher 3-4 prochains événements via Events Manager
- Utiliser le shortcode Events Manager existant ou une query personnalisée
- Lien vers la page calendrier complète

#### 4. Témoignages (carousel)

```html
<section class="home-testimonials" aria-labelledby="testimonials-title">
    <h2 id="testimonials-title">Témoignages</h2>
    <div
        class="carousel"
        role="region"
        aria-roledescription="carousel"
        aria-label="Témoignages de participants"
    >
        <div class="carousel__controls">
            <button class="carousel__prev" aria-label="Témoignage précédent">←</button>
            <button class="carousel__pause" aria-label="Mettre en pause le défilement">⏸</button>
            <button class="carousel__next" aria-label="Témoignage suivant">→</button>
        </div>
        <div class="carousel__track" aria-live="polite">
            <!-- Slides générés via WP_Query sur kiyose_testimony -->
            <div role="group" aria-roledescription="slide" aria-label="1 sur 5">
                <?php get_template_part( 'template-parts/content', 'testimony' ); ?>
            </div>
        </div>
    </div>
</section>
```

**Spécifications carousel** (conformes WCAG 2.2 AA) :
- `role="region"` + `aria-roledescription="carousel"` + `aria-label`
- Chaque slide : `role="group"` + `aria-roledescription="slide"` + `aria-label="X sur Y"`
- Contrôles : précédent, suivant, pause/play
- `aria-live="polite"` sur le conteneur (passe à `"off"` si autoplay actif)
- Navigation clavier : flèches gauche/droite
- Autoplay désactivé par défaut si `prefers-reduced-motion: reduce`
- Le carousel charge les témoignages via `WP_Query` sur le CPT `kiyose_testimony`

#### 5. Newsletter

```html
<section class="home-newsletter" aria-labelledby="newsletter-title">
    <h2 id="newsletter-title">Restez informé·e</h2>
    <p>Recevez les prochaines dates et actualités de l'atelier.</p>
    <!-- Placeholder pour shortcode Brevo (PRD 0013) -->
</section>
```

- Section avec fond de couleur différenciée (taupe rosé ou beige)
- Le formulaire Brevo sera intégré dans le PRD 0013

#### 6. Actualités

```html
<section class="home-blog" aria-labelledby="blog-title">
    <h2 id="blog-title">Actualités</h2>
    <div class="home-blog__grid">
        <!-- 3 derniers articles via WP_Query -->
        <?php get_template_part( 'template-parts/content', 'blog' ); ?>
    </div>
    <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="button button--secondary">
        Toutes les actualités
    </a>
</section>
```

- Grille de 3 articles (1 colonne mobile, 3 desktop)
- Réutilise `content-blog.php` du PRD 0006
- Lien vers la page blog

### `template-parts/content-service.php`

Carte de service cliquable pour la section "4 piliers".

```html
<a href="<?php the_permalink(); ?>" class="service-card" aria-label="En savoir plus sur <?php the_title_attribute(); ?>">
    <div class="service-card__image">
        <?php the_post_thumbnail( 'kiyose-service-card' ); ?>
    </div>
    <div class="service-card__content">
        <h3 class="service-card__title"><?php the_title(); ?></h3>
        <p class="service-card__description"><?php the_excerpt(); ?></p>
    </div>
</a>
```

### `assets/js/modules/carousel.js`

Module ES6 pour le carousel accessible :

**API** :
```javascript
class KiyoseCarousel {
    constructor( element ) { ... }
    init() { ... }
    goToSlide( index ) { ... }
    nextSlide() { ... }
    prevSlide() { ... }
    toggleAutoplay() { ... }
    destroy() { ... }
}
```

**Fonctionnalités** :
- Navigation par boutons (précédent/suivant)
- Navigation clavier (flèches gauche/droite quand le carousel a le focus)
- Autoplay avec bouton pause/play
- Autoplay désactivé si `prefers-reduced-motion: reduce`
- Mise à jour dynamique des `aria-label` ("X sur Y")
- `aria-live` toggle entre `"polite"` et `"off"` selon l'état autoplay

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur tous les fichiers PHP créés

### Tests manuels — Sections
- [ ] Hero affiché avec tagline, sous-titre, CTA
- [ ] 4 cartes de services affichées et cliquables
- [ ] Prochains événements affichés (3-4) — si Events Manager est configuré
- [ ] Carousel de témoignages fonctionne (navigation, affichage)
- [ ] Section newsletter présente (placeholder)
- [ ] 3 derniers articles de blog affichés

### Tests manuels — Carousel
- [ ] Boutons précédent/suivant fonctionnent
- [ ] Pause/play fonctionne
- [ ] Navigation clavier (flèches) fonctionne
- [ ] `aria-live` passe à `"off"` quand autoplay est actif
- [ ] Autoplay désactivé quand `prefers-reduced-motion: reduce`
- [ ] `aria-label` mis à jour sur chaque slide

### Tests manuels — Accessibilité
- [ ] Un seul `<h1>` (dans le hero)
- [ ] Hiérarchie h1 → h2 → h3 respectée
- [ ] Toutes les sections ont un `aria-labelledby` ou `aria-label`
- [ ] Cartes de services ont des `aria-label` descriptifs
- [ ] Navigation clavier complète sur toute la page

### Tests manuels — Responsive
- [ ] Hero : texte lisible sur mobile, image/fond adapté
- [ ] Services : 1 col mobile → 2 col tablet → 4 col desktop
- [ ] Actualités : 1 col mobile → 3 col desktop
- [ ] Carousel : fonctionnel sur mobile (swipe non requis, boutons suffisent)

## Hors périmètre

- Contenu textuel final (le client fournira les textes définitifs)
- Formulaire newsletter Brevo (PRD 0013)
- Éléments décoratifs Kintsugi (PRD 0014)
- Images finales des services (placeholders acceptables)
