# PRD 0013 — Intégrations plugins

## Objectif

Intégrer visuellement les plugins tiers (Brevo newsletter, Contact Form 7, Events Manager) dans le design system du thème. Mettre en place le formulaire newsletter dans le footer et sur la homepage.

## Dépendances

- **PRD 0003** : Footer en place (emplacement newsletter).
- **PRD 0008** : Homepage (section newsletter).

## Pré-requis

- **Brevo** : Plugin déjà installé et configuré.
- **CF7** : Plugin installé (cf. PRD 0010).
- **Events Manager** : Plugin déjà installé (cf. PRD 0012).

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
└── assets/
    └── css/
        └── components/
            ├── brevo-override.css     # Override styles formulaire Brevo
            └── plugins-common.css     # Styles communs aux plugins
```

### Fichiers modifiés

- `footer.php` — Ajouter le shortcode Brevo dans la section newsletter
- `templates/page-home.php` — Ajouter le shortcode Brevo dans la section newsletter
- `inc/enqueue.php` — Charger les CSS d'override des plugins

## Spécifications détaillées

### Newsletter Brevo

**Intégration** :
Le formulaire Brevo est intégré via son shortcode WordPress (fourni par le plugin Brevo). Le shortcode est placé :
1. Dans le footer (`footer.php`) — version compacte
2. Sur la homepage (`templates/page-home.php`) — version plus large

**Champs du formulaire** :
- Email (requis)
- Prénom (requis)
- Nom (optionnel)
- Checkbox RGPD (consentement explicite)

**Styling** :
Le plugin Brevo génère son propre markup. Les styles doivent être overridés pour s'intégrer au design system :

- Champs : bordures de la palette, focus visible, taille 44px min
- Labels : visibles, police Nunito
- Bouton submit : style `.button--primary`
- Messages de succès/erreur : couleurs accessibles avec contraste ≥ 4.5:1
- Layout footer : formulaire compact, possiblement inline (email + bouton sur une ligne)
- Layout homepage : formulaire plus spacieux, labels au-dessus des champs

**RGPD** :
- Le double opt-in est géré côté Brevo (configuration du plugin, pas du thème)
- La checkbox de consentement doit être visible et accessible

### Override CF7 (complément PRD 0010)

Si des ajustements CSS supplémentaires sont nécessaires après le PRD 0010, les ajouter ici.

### Override Events Manager (complément PRD 0012)

Si des ajustements CSS supplémentaires sont nécessaires après le PRD 0012, les ajouter ici. Notamment pour le widget "Prochains événements" sur la homepage (PRD 0008).

### `plugins-common.css`

Styles partagés entre les overrides de plugins :
- Normalisation des champs de formulaire (tous les plugins)
- Normalisation des boutons (tous les plugins)
- Masquage des éléments non souhaités générés par les plugins

## Tests et validation

### Tests manuels
- [ ] Le formulaire Brevo s'affiche dans le footer avec le bon styling
- [ ] Le formulaire Brevo s'affiche sur la homepage avec le bon styling
- [ ] L'inscription newsletter fonctionne (email reçu dans Brevo)
- [ ] Le formulaire CF7 est correctement stylé (si ajustements post-PRD 0010)
- [ ] Les événements Events Manager sont correctement stylés sur la homepage et la page calendrier

### Tests manuels — Accessibilité
- [ ] Tous les champs des formulaires plugins ont des labels
- [ ] Navigation clavier complète dans les formulaires
- [ ] Messages d'erreur accessibles (contraste, association aux champs)
- [ ] Focus visible sur tous les éléments interactifs des plugins

### Tests manuels — Responsive
- [ ] Formulaire newsletter compact dans le footer sur mobile
- [ ] Formulaire newsletter spacieux sur la homepage desktop
- [ ] Tous les formulaires plugins utilisables sur mobile

## Hors périmètre

- Configuration des plugins (Brevo, CF7, Events Manager)
- Création du formulaire CF7 (manuelle dans l'admin)
- Configuration du double opt-in Brevo
