# PRD 0010 — Page contact

## Objectif

Créer le template de la page contact avec le formulaire Contact Form 7, les informations de contact, et les liens réseaux sociaux.

## Dépendances

- **PRD 0005** : Templates de base.

## Pré-requis

- **Installation du plugin Contact Form 7** : Le plugin n'est pas encore installé. Il faudra l'installer via l'admin WordPress (Extensions > Ajouter) avant d'utiliser ce template.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── templates/
│   └── page-contact.php               # Template page contact
└── assets/
    └── css/
        └── components/
            ├── contact-page.css       # Styles page contact
            └── cf7-override.css       # Override styles Contact Form 7
```

## Spécifications détaillées

### `templates/page-contact.php`

**Header du fichier** :
```php
<?php
/*
Template Name: Page de contact
*/
```

**Structure** :
```
get_header()

<article class="contact-page">
    <header class="contact-page__header">
        <h1 class="contact-page__title"><?php the_title(); ?></h1>
        <p class="contact-page__intro"><!-- Texte d'introduction --></p>
    </header>

    <div class="contact-page__layout">
        <!-- Colonne formulaire -->
        <div class="contact-page__form">
            <h2>Envoyez-moi un message</h2>
            <?php the_content(); ?>
            <!-- Le shortcode CF7 sera dans le contenu de la page WordPress -->
        </div>

        <!-- Colonne informations -->
        <aside class="contact-page__info" aria-label="Informations de contact">
            <h2>Coordonnées</h2>

            <div class="contact-info">
                <div class="contact-info__item">
                    <h3>Téléphone</h3>
                    <a href="tel:+33658373205">06 58 37 32 05</a>
                </div>

                <div class="contact-info__item">
                    <h3>Email</h3>
                    <a href="mailto:contact@latelierkiyose.fr">contact@latelierkiyose.fr</a>
                </div>

                <div class="contact-info__item">
                    <h3>Adresse</h3>
                    <address>
                        Le Grand Vron<br>
                        86510 Brux<br>
                        Nouvelle-Aquitaine
                    </address>
                </div>

                <div class="contact-info__social">
                    <h3>Réseaux sociaux</h3>
                    <!-- Liens Facebook, Instagram, LinkedIn -->
                </div>
            </div>
        </aside>
    </div>
</article>

get_footer()
```

**Points clés** :
- Layout en 2 colonnes : formulaire (2/3) + infos (1/3) en desktop, empilé en mobile
- Le formulaire CF7 est inséré via `the_content()` — le shortcode CF7 est placé dans l'éditeur WordPress
- Les informations de contact proviennent de `references/business-info.json`
- Le lien téléphone utilise le protocole `tel:` pour les mobiles
- L'adresse utilise l'élément sémantique `<address>`

### Styling Contact Form 7

CF7 génère son propre markup HTML. Les styles doivent être overridés pour matcher le design system.

**Classes CF7 à styler** :
- `.wpcf7-form` — Formulaire principal
- `.wpcf7-form-control` — Champs de formulaire
- `.wpcf7-submit` — Bouton submit
- `.wpcf7-not-valid-tip` — Messages d'erreur inline
- `.wpcf7-response-output` — Message de réponse (succès/erreur)
- `.wpcf7-validation-errors` — Erreurs de validation

**Styles à appliquer** :
- Champs : utiliser les styles `.form-input` du design system (bordure, focus, taille 44px min)
- Labels : visibles au-dessus des champs, pas uniquement des placeholders
- Bouton submit : utiliser les styles `.button--primary`
- Messages d'erreur : couleur rouge avec contraste ≥ 4.5:1, associés aux champs via `aria-describedby`
- Message de succès : couleur verte/positive avec contraste suffisant

### Configuration CF7 recommandée

Le formulaire CF7 à créer dans l'admin WordPress :
- Champ : Nom (requis)
- Champ : Email (requis)
- Champ : Téléphone (optionnel)
- Champ : Objet (select : Renseignements, Réservation, Autre)
- Champ : Message (textarea, requis)
- Protection : honeypot + reCAPTCHA v3 (si configuré)

Cette configuration est documentaire — elle se fait manuellement dans l'admin CF7.

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur les fichiers PHP créés

### Tests manuels
- [ ] Le template "Page de contact" apparaît dans le menu des templates
- [ ] Le formulaire CF7 s'affiche correctement (si le plugin est installé et le shortcode placé)
- [ ] Les informations de contact sont affichées : téléphone, email, adresse
- [ ] Le lien téléphone fonctionne sur mobile
- [ ] Le lien email ouvre le client mail
- [ ] Les liens réseaux sociaux fonctionnent

### Tests manuels — Accessibilité
- [ ] Tous les champs du formulaire ont des labels visibles
- [ ] Les messages d'erreur CF7 sont accessibles
- [ ] Navigation clavier complète dans le formulaire
- [ ] Focus visible sur tous les champs et le bouton submit
- [ ] Contraste des messages d'erreur ≥ 4.5:1

### Tests manuels — Responsive
- [ ] Layout 2 colonnes en desktop, empilé en mobile
- [ ] Formulaire utilisable sur mobile (champs, bouton accessibles)
- [ ] Cibles tactiles ≥ 44x44px

## Hors périmètre

- Installation et configuration de CF7 (manuelle dans l'admin)
- Configuration reCAPTCHA (dépend d'une clé API)
- Carte interactive (pas souhaitée — adresse en texte uniquement)
