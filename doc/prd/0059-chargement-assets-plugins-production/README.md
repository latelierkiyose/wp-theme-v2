# PRD 0059 — Chargement production des assets plugins et scripts externes

- **Statut** : prêt à l'implémentation
- **Criticité** : moyenne
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

La revue pré-production a mesuré sur la home environ :

- `25` stylesheets ;
- `32` scripts ;
- plusieurs scripts externes Brevo/reCAPTCHA ;
- des chargements reCAPTCHA dupliqués quand plusieurs formulaires newsletter sont présents.

Ce PRD remplace le PRD 0021 comme source active pour l'optimisation du chargement front. Il reprend uniquement les points encore utiles de 0021 et les recentre sur la revue pré-production : réduire les duplications, charger les assets plugins seulement là où ils sont nécessaires, et conserver les acquis du registre d'assets conditionnels sans casser Events Manager, Contact Form 7, Brevo ou reCAPTCHA.

Les éléments déjà traités par PRD 0045, notamment le registre d'assets conditionnels, les dépendances `kiyose-main` et le preload de police Caveat remplacé, restent des prérequis de non-régression plutôt qu'un nouveau périmètre d'implémentation.

## Problème à résoudre

La page charge trop de ressources avant production. Les scripts tiers répétés augmentent :

- le temps de chargement ;
- le risque d'erreurs réseau ;
- la surface de scripts externes ;
- les interactions imprévisibles entre plusieurs instances de formulaires.

Même si les assets sont minifiés, leur nombre et leur duplication restent un risque Core Web Vitals.

## Objectif

Réduire le coût front des plugins sans supprimer de fonctionnalité :

1. aucun script reCAPTCHA/Brevo n'est chargé deux fois sur la même page ;
2. les assets Events Manager ne chargent que sur les pages qui affichent des événements ou réservations ;
3. les assets Contact Form 7 ne chargent que sur la page contact ou les contenus qui contiennent son shortcode ;
4. les assets Brevo ne chargent que sur les pages qui affichent un formulaire newsletter ;
5. la home conserve ses fonctionnalités visibles.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Relation PRD 0021 | Remplacement | PRD 0059 devient le plan actif ; PRD 0021 est conservé seulement comme historique annulé |
| Priorité | Dédoublonner les scripts tiers avant micro-optimisations | Risque le plus visible et le plus coûteux |
| Plugins | Utiliser `wp_dequeue_script/style` avec conditions explicites | Approche WordPress standard et réversible |
| Détection | Réutiliser le registre d'assets conditionnels existant si possible | PRD 0045 a déjà posé cette architecture |
| Sécurité | Ne pas retirer reCAPTCHA sur les pages avec formulaire actif | Le contrôle anti-spam doit rester disponible |

## Backport depuis PRD 0021

| Élément PRD 0021 | Décision dans PRD 0059 | Justification |
|---|---|---|
| Dequeue Events Manager hors périmètre | À intégrer | Le chargement conditionnel plugin est au cœur de ce PRD |
| Preconnect reCAPTCHA conditionnel | À intégrer | Utile uniquement sur les pages avec formulaire actif |
| jQuery en footer | Candidat, pas obligatoire | Peut casser des plugins qui injectent du jQuery inline dans le `<head>` |
| CSS critique inline et chargement async CSS | Candidat après mesure | Gain possible, mais dette de maintenance et risque de FOUC |
| CSS conditionnels thème, dépendances `kiyose-main`, preload Caveat | Déjà couvert par PRD 0045 | À vérifier comme non-régression, pas à réimplémenter |
| Ancienne baseline Lighthouse 0021 | À remplacer | Les mesures datent d'avant plusieurs PRD terminés |

## Spécifications

### 1. Mesurer l'état initial

Créer une commande ou documenter une méthode reproductible pour compter :

- stylesheets rendues ;
- scripts rendus ;
- scripts externes ;
- doublons d'URL.

Pages minimum :

- `/`
- `/contact/`
- `/agenda/`
- un article de blog ;
- une page service.

La mesure peut être un script Node/Chrome local, un rapport Lighthouse ou une checklist manuelle documentée.

### 2. Préserver les acquis du registre d'assets

Avant toute optimisation plugin, vérifier que les acquis de PRD 0045 restent en place :

- les assets thème conditionnels ne redeviennent pas globaux ;
- `kiyose-main` dépend seulement des handles globaux nécessaires ;
- aucun preload ne pointe vers `caveat-v21-latin-regular.woff2` ;
- les polices réellement présentes restent préchargées ;
- les styles critiques du header/navigation/footer ne sont pas retardés.

Ces points servent de garde-fou. Ils ne doivent pas être réimplémentés dans ce PRD sauf régression constatée.

### 3. Dédoublonner Brevo/reCAPTCHA

Coordonner avec le PRD 0060.

Règles attendues :

- une seule URL `https://www.google.com/recaptcha/api.js?...` par page ;
- une seule URL `https://www.gstatic.com/recaptcha/.../recaptcha__*.js` chargée par page ;
- une seule instance du SDK Brevo si le SDK est nécessaire ;
- aucun double chargement dû à deux formulaires newsletter sur la même page ;
- ne pas exposer de clé ou token supplémentaire dans le code thème.

Si le plugin Brevo force le chargement plusieurs fois, ajouter un dequeue/deregister défensif documenté et testé sur staging.

Ajouter un preconnect reCAPTCHA uniquement quand un formulaire actif peut charger reCAPTCHA :

- Contact Form 7 sur `/contact/` ou contenu avec shortcode ;
- formulaire newsletter Brevo si reCAPTCHA est activé côté plugin ;
- formulaire de réservation Events Manager si reCAPTCHA est activé.

Ne pas ajouter de preconnect global sur toutes les pages.

### 4. Charger conditionnellement les assets Events Manager

S'appuyer sur la détection existante dans `latelierkiyose/inc/enqueue.php`.

Pages où Events Manager peut charger :

- home si la section prochains événements est rendue ;
- `/agenda/` ;
- pages détail `event` ;
- pages réservations ou taxonomies Events Manager conservées par PRD 0059 ;
- contenu contenant explicitement un shortcode Events Manager.

Pages où Events Manager ne doit pas charger :

- pages service sans shortcode Events Manager ;
- blog archive hors affichage événement ;
- pages légales ;
- 404 et recherche sauf contenu spécifique.

Le dequeue des assets natifs Events Manager doit compléter, pas remplacer, la détection thème existante. La home reste un cas explicite car elle peut rendre `[events_list ...]` depuis le template PHP, sans shortcode dans le contenu éditorial.

### 5. Charger conditionnellement Contact Form 7

CF7 ne doit charger que si :

- la page est `/contact/` ;
- le contenu contient un shortcode CF7 ;
- un template rend explicitement le formulaire.

Vérifier que la page contact conserve :

- validation côté client si active ;
- soumission AJAX si configurée ;
- messages d'erreur/succès.

### 6. Charger conditionnellement Brevo

Brevo ne doit charger que si un formulaire newsletter est réellement rendu dans le DOM.

Cas attendus :

- footer avec newsletter active ;
- home avec newsletter active ;
- overlay newsletter si conservé ;
- pages sans newsletter si le footer ne rend pas le formulaire.

Si le footer contient toujours la newsletter sur toutes les pages, documenter ce choix : dans ce cas l'optimisation Brevo porte sur la déduplication, pas sur l'absence totale du script.

### 7. Garder les micro-optimisations risquées derrière mesure

Les optimisations suivantes ne doivent être appliquées qu'après mesure avant/après et validation plugin :

- déplacer jQuery en footer seulement si Events Manager, CF7, Brevo et reCAPTCHA fonctionnent encore ;
- introduire un CSS critique inline seulement si la mesure confirme un gain net ;
- charger des CSS non critiques via `media="print"` seulement avec fallback `<noscript>` ;
- documenter toute dette de synchronisation si un fichier `critical.css` est créé.

Si une de ces optimisations casse un plugin ou crée un flash visuel visible, elle doit être abandonnée sans bloquer les gains principaux de ce PRD.

### 8. Ajouter des tests de registre/conditions

Compléter les tests d'enqueue existants :

- Events Manager ne charge pas sur une page standard sans shortcode ;
- Events Manager charge sur agenda/home/event ;
- CF7 charge sur contact et pas sur une page service standard ;
- Brevo ne s'enregistre pas deux fois pour deux emplacements newsletter ;
- les handles thème conditionnels restent cohérents.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/inc/enqueue.php` | Ajuster conditions et dequeues plugins |
| `latelierkiyose/inc/components.php` | Réutiliser les helpers newsletter du PRD 0060 si nécessaire |
| `latelierkiyose/tests/Test_Enqueue.php` | Ajouter les cas de conditions plugins |
| `tests/build-check.test.js` | Ajouter un check uniquement si une mesure statique est possible |
| `doc/release/README.md` | Ajouter une étape de validation asset count / Lighthouse |

## Performance

Objectifs de validation :

- home : aucun doublon d'URL script externe ;
- contact : CF7 + Brevo/reCAPTCHA uniquement si nécessaires ;
- agenda : Events Manager présent, CF7 absent ;
- page service : pas d'assets Events Manager/CF7 si aucun shortcode ;
- Lighthouse Performance à mesurer sur staging après correction.

Ce PRD ne fixe pas un score Lighthouse obligatoire à lui seul, car le score dépendra aussi des images, du serveur et des plugins de cache/compression.

## Accessibilité et sécurité

- Ne pas supprimer les scripts nécessaires aux messages d'erreur/succès des formulaires.
- Ne pas supprimer reCAPTCHA sur les formulaires publics actifs.
- Ne pas masquer des erreurs plugin en désactivant leur JS.
- Ne pas logguer les clés ou tokens Brevo/reCAPTCHA.

## Documentation

Mettre à jour `doc/release/README.md` avec :

- une mesure avant/après sur staging ;
- une vérification console navigateur sans erreur ;
- une vérification formulaire contact/newsletter ;
- une vérification agenda/réservation.

## Hors périmètre

- Optimiser les images : voir le PRD image existant ou futur.
- Refondre tout le chemin critique CSS au-delà des micro-optimisations mesurées.
- Supprimer jQuery si les plugins actifs en dépendent.
- Remplacer Brevo, CF7 ou Events Manager.
- Résoudre la panne locale reCAPTCHA connue.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| Dequeue trop agressif casse un plugin | Valider chaque plugin sur une page qui l'utilise |
| Le footer newsletter rend Brevo nécessaire partout | Documenter cette décision ou rendre le footer newsletter conditionnel |
| Les scripts tiers changent leurs handles | Identifier les handles réellement enregistrés par les plugins installés |
| L'optimisation locale ne reflète pas la production | Rejouer les mesures sur staging avec les mêmes plugins |

## Tests et validation

### Validation automatisée

- [ ] Tests d'enqueue Events Manager ajoutés.
- [ ] Tests d'enqueue CF7 ajoutés.
- [ ] Tests d'enqueue Brevo/reCAPTCHA ajoutés si les handles sont contrôlables.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `./bin/phpcs.sh` passe.
- [ ] `make test` passe.

### Validation manuelle

- [ ] Home : aucun script externe dupliqué.
- [ ] Contact : formulaire CF7 utilisable, pas d'erreur console non connue.
- [ ] Agenda : événements affichés, filtres/réservations fonctionnent si présents.
- [ ] Page service : pas d'assets Events Manager ou CF7 inutiles.
- [ ] Newsletter : inscription testée sur staging avec reCAPTCHA valide.
- [ ] Lighthouse ou Chrome Performance exécuté avant/après.
