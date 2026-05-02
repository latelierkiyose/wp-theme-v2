# PRD 0062 — CTAs configurables des pages de service

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Les pages qui utilisent le template `Page de service` affichent deux boutons en bas de page :

1. « Me contacter » ;
2. « Voir le calendrier ».

Avant cette évolution, les URLs étaient codées en dur dans le template. Le lien calendrier pointait vers une ancienne URL et pouvait devenir un lien mort dès que la page cible changeait de slug, était remplacée ou n'existait pas sur l'installation.

Le site utilise aussi Events Manager pour publier les ateliers et rendez-vous. Chaque page de service doit pouvoir orienter vers le calendrier complet ou vers une vue filtrée par catégories d'événements. Si aucune catégorie n'est précisée sur un service, le calendrier doit rester complet.

## Objectif

Rendre les deux boutons du template service configurables sans modifier le thème :

1. permettre de choisir une page WordPress existante pour le bouton « Me contacter » ;
2. permettre de choisir une page WordPress existante pour le bouton « Voir le calendrier » ;
3. permettre d'associer zéro, une ou plusieurs catégories Events Manager à une page de service ;
4. transmettre ces catégories à la page calendrier quand elles sont configurées ;
5. conserver le comportement « rien de précisé = tout ».

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Configuration des pages cibles | Options du Customizer avec contrôles `dropdown-pages` | Évite les liens en dur et reste accessible aux personnes qui administrent le site |
| Portée des boutons | Réglage global du thème | Les deux boutons sont communs au template service, pas spécifiques à un bloc de contenu |
| Catégories par service | Meta box sur les pages utilisant le template service | Le filtre dépend du service édité |
| Absence de catégorie | Aucun filtre transmis | Le calendrier affiche tous les événements |
| Filtre calendrier | Paramètre public `kiyose_event_categories` + shortcode `[kiyose_events_list]` | Le template service reste découplé d'Events Manager et la page calendrier reste éditable |
| Page non configurée ou invalide | Le bouton concerné est masqué | Évite les liens morts dans le frontend |

## Spécifications

### 1. Réglages Customizer

Ajouter une section « Boutons des pages de service » dans le Customizer.

Réglages :

| Réglage | Contrôle | Rôle |
|---|---|---|
| `kiyose_service_contact_page_id` | Liste de pages | Page cible du bouton « Me contacter » |
| `kiyose_service_calendar_page_id` | Liste de pages | Page cible du bouton « Voir le calendrier » |

Règles :

- les IDs sont sanitizés avec `absint()` ;
- seules les pages publiées sont utilisées au rendu ;
- si une page est supprimée, dépubliée ou non configurée, le bouton correspondant n'est pas affiché ;
- aucune URL en dur ne reste dans le template service.

### 2. Boutons du template service

Le template `templates/page-services.php` récupère les liens via un helper thème.

Règles :

- afficher le footer CTA uniquement si au moins un bouton valide existe ;
- conserver les libellés « Me contacter » et « Voir le calendrier » ;
- échapper les URLs et libellés ;
- conserver les classes CSS existantes.

### 3. Catégories optionnelles par service

Ajouter une meta box sur les pages utilisant `templates/page-services.php`.

Règles :

- afficher les catégories de la taxonomie Events Manager `event-categories` ;
- permettre de cocher plusieurs catégories ;
- sauvegarder uniquement des slugs existants ;
- supprimer la meta si aucune catégorie n'est sélectionnée ;
- si aucune catégorie n'est sélectionnée, la page calendrier n'est pas filtrée.

### 4. Shortcode calendrier filtrable

Ajouter le shortcode :

```text
[kiyose_events_list]
```

Comportement :

- sans paramètre `kiyose_event_categories`, rendre la liste Events Manager complète ;
- avec un ou plusieurs slugs valides, transmettre `category="slug-1,slug-2"` au shortcode Events Manager ;
- ignorer les slugs inconnus ou malveillants ;
- charger les assets Events Manager quand le shortcode est présent.

### 5. Documentation

Mettre à jour la documentation utilisateur :

- choix du template de page ;
- recette de création d'une page de service ;
- documentation des shortcodes ;
- documentation calendrier / événements.

Mettre à jour la documentation de migration :

- configuration des deux pages cibles dans le Customizer ;
- migration des pages calendrier vers `[kiyose_events_list]` ;
- sélection optionnelle des catégories sur les pages de service ;
- diagnostic rollback en cas de bouton absent ou de filtre non appliqué.

## Accessibilité et sécurité

- Les liens restent de vrais éléments `<a>`, accessibles au clavier.
- Les boutons conservent les styles de focus existants.
- Les sorties frontend sont échappées avec `esc_url()` et `esc_html()`.
- Les données admin sont protégées par nonce et capability.
- Les catégories reçues depuis l'URL sont validées contre les termes existants avant usage.
- Aucun secret ni donnée sensible n'est manipulé.

## Hors périmètre

- Ajouter des libellés de boutons configurables.
- Créer un bloc Gutenberg dédié.
- Ajouter une option par page pour remplacer individuellement les URLs des deux boutons.
- Remplacer Events Manager ou modifier ses templates internes.
- Créer une page calendrier automatiquement.

## Tests et validation

### Tests automatisés

- [x] Les réglages Customizer sont enregistrés avec des contrôles de type page.
- [x] Les boutons utilisent les pages configurées et masquent les pages invalides.
- [x] Le lien calendrier reçoit les catégories du service quand elles existent.
- [x] Une absence de catégories ne filtre pas le calendrier.
- [x] Les slugs inconnus ou dangereux sont ignorés.
- [x] La meta box service sauvegarde uniquement les catégories valides.
- [x] `[kiyose_events_list]` rend la liste complète sans filtre.
- [x] `[kiyose_events_list]` transmet les catégories valides à Events Manager.
- [x] Les assets Events Manager chargent avec `[kiyose_events_list]`.
- [x] Le template service n'a plus d'URL CTA codée en dur.

### Validation finale

- [x] `./bin/phpunit.sh --testdox` passe.
- [x] `./bin/phpcs.sh` passe.
- [x] `make test` passe.
- [x] Documentation utilisateur mise à jour.
- [x] Documentation de migration mise à jour.
