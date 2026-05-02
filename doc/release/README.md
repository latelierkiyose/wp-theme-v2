# Mise en production — L'Atelier Kiyose

Procédure complète pour déployer le thème « L'Atelier Kiyose » sur le site de production **https://www.latelierkiyose.fr/** et migrer le contenu existant.

## À qui s'adresse ce document

Personne qui va installer le thème en production et reconnecter le contenu existant (pages, témoignages, formulaires) aux nouvelles conventions du thème. Pas besoin d'être développeur ; connaissance de l'admin WordPress et d'un client SFTP (type FileZilla) suffit.

**Durée estimée** : 2 à 3 heures (validation incluse).

## Documents associés

| Document | Quand le consulter |
|---|---|
| [`plugins.md`](plugins.md) | Étape 3 (requis) et étape 7 (recommandés) |
| [`content-migration.md`](content-migration.md) | Étape 6 |
| [`rollback.md`](rollback.md) | En cas de pépin à n'importe quelle étape |
| [`../validation-checklist.md`](../validation-checklist.md) | Étape 8 |
| [`../user/`](../user/) | Guides pour les rédacteurices (référence) |

---

## 🗺️ Vue d'ensemble — 10 étapes

| # | Étape | Où ? | Durée | Bloquant ? |
|---|---|---|---|---|
| 1 | Sauvegarde complète (DB + fichiers) | admin WP / SFTP | 15-30 min | ⚠️ **oui** |
| 2 | Documenter l'état actuel | admin WP | 10 min | recommandé |
| 3 | Installer les 3 plugins **requis** | admin WP | 20 min | ⚠️ **oui** |
| 4 | Uploader le thème | SFTP ou admin WP | 10 min | **oui** |
| 5 | Activer le thème + config de base | admin WP | 15 min | **oui** |
| 6 | Migrer le contenu existant | admin WP | 45-60 min | **oui** |
| 7 | Installer les plugins recommandés | admin WP | 20 min | non |
| 8 | Valider (a11y, perf, formulaires) | navigateur | 30-45 min | **oui** |
| 9 | Bascule publique + vider cache | admin WP | 5 min | **oui** |
| 10 | Monitoring première semaine | divers | étalé | recommandé |

---

## ⚠️ Prérequis bloquants — à valider AVANT de commencer

Ne pas entamer la procédure tant que l'un des prérequis suivants n'est pas prêt :

- [ ] **Accès SFTP/SSH** au serveur de production (vérifier la connexion avec un `ls` dans `/wp-content/themes/`)
- [ ] **Accès admin WordPress** avec un rôle Administrateur
- [ ] **Artefact ZIP du thème** téléchargé depuis [Releases GitHub](https://github.com/latelierkiyose/wp-theme-v2/releases) : fichier nommé `latelierkiyose-theme-*.zip` ⚠️ **pas** « Source code (zip) ». Cet artefact est généré par la CI après un build explicite des assets CSS/JS.
- [ ] **Fenêtre de maintenance** (2-3 h en heures creuses) communiquée au commanditaire
- [ ] **Logins Brevo, Events Manager, Contact Form 7** disponibles (pour récupérer les formulaires/événements existants ou en créer de nouveaux)
- [ ] **Plan de rollback lu** : [`rollback.md`](rollback.md)

---

## Étape 1 — Sauvegarde complète ⚠️

**Cette étape est non négociable.** Sans sauvegarde, il n'y a pas de retour arrière possible en cas de pépin.

1. Installer le plugin **UpdraftPlus** si pas déjà présent (Extensions → Ajouter → « UpdraftPlus »).
2. Réglages → Sauvegardes UpdraftPlus → **Sauvegarder maintenant**.
3. Cocher **Base de données** et **Fichiers (plugins, thèmes, uploads)**.
4. Télécharger les archives générées sur votre ordinateur local.
5. Vérifier que le fichier `.zip` ou `.sql` s'ouvre correctement (pas vide, poids cohérent).

**Alternative sans plugin** :

- Base de données : export via phpMyAdmin → onglet « Exporter » → méthode rapide, format SQL.
- Fichiers : télécharger `/wp-content/` en SFTP (themes + uploads + plugins).

---

## Étape 2 — Documenter l'état actuel

Avant de modifier quoi que ce soit, relever l'existant pour pouvoir comparer après la migration (et pour le rollback) :

1. Thème actif : Apparence → Thèmes → noter le nom et la version affichée.
2. Plugins actifs : Extensions → Extensions installées → filtre « Actif » → capture d'écran ou copier la liste.
3. Réglages → Lecture → noter la page d'accueil et la page des articles assignées.
4. Apparence → Menus → noter quel menu est assigné à quel emplacement.
5. Capture d'écran de la homepage actuelle (desktop + mobile) pour référence visuelle.

Avec WP-CLI (si disponible) :

```bash
wp theme list --status=active
wp plugin list --status=active
wp core version
```

---

## Étape 3 — Installer les plugins REQUIS ⚠️

**Installer ces trois plugins AVANT d'activer le thème**, sinon certaines sections s'affichent en texte brut.

→ **Référence complète avec configurations** : [`plugins.md`](plugins.md) — section « Plugins REQUIS »

Résumé à cocher :

- [ ] **Brevo** installé + activé + formulaire d'**ID 1** créé (double opt-in RGPD)
- [ ] **Events Manager** installé + activé + au moins 1 événement à venir créé
- [ ] **Contact Form 7** installé + activé + formulaire de contact conservé ou recréé (avec consentement RGPD)

> ⚠️ Le shortcode Brevo attendu est `[sibwp_form id=1]` — **pas** `[brevo_form]` (erreur historique dans la doc). Si l'ID n'est pas 1, voir [`plugins.md § Que faire si l'ID n'est pas 1`](plugins.md#1-brevo-newsletter).

---

## Étape 4 — Uploader le thème

### Via SFTP (recommandé)

1. Connexion SFTP au serveur.
2. Aller dans `/wp-content/themes/`.
3. Uploader le ZIP dans un dossier temporaire (ex. `temp-upload/`).
4. En SSH, extraire :
   ```bash
   cd /path/to/wp-content/themes/temp-upload/
   unzip latelierkiyose-theme-*.zip
   mv latelierkiyose ../latelierkiyose
   cd .. && rm -rf temp-upload
   ```
5. Ajuster permissions si besoin : `chmod -R 755 latelierkiyose/` et `chown -R www-data:www-data latelierkiyose/` (adapter l'utilisateur au serveur).

> Préparation développeur : si un ZIP doit exceptionnellement être préparé hors CI, lancer `npm run build`, puis `npm run build:check` avant de créer l'archive. Ne jamais générer les assets directement sur le serveur de production.

### Via admin WordPress (alternative)

1. Apparence → Thèmes → **Ajouter** → **Téléverser un thème**.
2. Choisir le ZIP → **Installer maintenant**.
3. ⚠️ **Ne pas activer tout de suite** — on active à l'étape suivante après vérifications.

### Vérification pré-activation

- [ ] Le thème « L'Atelier Kiyose » apparaît dans Apparence → Thèmes
- [ ] La capture d'écran s'affiche
- [ ] Version, auteur et description sont corrects
- [ ] Aucune erreur PHP dans les logs serveur

---

## Étape 5 — Activer le thème + config de base

### 5.1 Activation

1. Apparence → Thèmes → survoler « L'Atelier Kiyose » → **Activer**.
2. Ouvrir immédiatement la homepage dans un onglet privé.
3. ✅ Si le site s'affiche (même sans contenu migré) → passer à 5.2.
4. 🛑 Si écran blanc → [`rollback.md`](rollback.md) niveau 1 et diagnostic des logs PHP.

### 5.2 Assigner le menu

1. Apparence → Menus → sélectionner le menu existant.
2. En bas → **Réglages du menu** → cocher l'emplacement **« Menu principal »** (slug `primary`).
3. Enregistrer.
4. Rafraîchir la homepage : le menu doit apparaître dans le header.

### 5.3 Uploader le logo

1. Apparence → Personnaliser → Identité du site → **Sélectionner le logo**.
2. Uploader le logo (SVG recommandé ou PNG transparent, largeur 200-300 px).
3. Publier.

### 5.4 Page d'accueil statique

1. Réglages → Lecture → **Une page statique**.
2. Page d'accueil : page « Accueil ».
3. Page des articles : page « Blog » ou « Actualités ».
4. Enregistrer.

### 5.5 Permaliens

1. Réglages → Permaliens → **Titre de la publication** (`/%postname%/`).
2. Enregistrer (cliquer même si déjà sélectionné — ça force un refresh des rewrite rules).

---

## Étape 6 — Migrer le contenu existant

→ **Procédure détaillée** : [`content-migration.md`](content-migration.md)

Synthèse des actions à réaliser :

- [ ] Assigner un template à chaque page (tableau dans [`content-migration.md § 1`](content-migration.md#1-assigner-un-template-à-chaque-page))
- [ ] Remplir les meta fields de la homepage (titres, slogans, Q&R, overlay)
- [ ] Remplir la photo de la page Contact (+ texte alt)
- [ ] Recréer les témoignages en CPT `kiyose_testimony` avec leur contexte
- [ ] Remplacer les anciens blocs « signets » par `[kiyose_signets][/kiyose_signets]`
- [ ] Insérer `[contact-form-7 id="..."]` dans la page Contact
- [ ] Insérer `[events_list ...]` dans la page Calendrier
- [ ] Régénérer les miniatures (Regenerate Thumbnails ou `wp media regenerate`)
- [ ] Vérifier le slug de la page calendrier = `calendrier-tarifs` (sinon les CTA des pages de service 404)

---

## Étape 7 — Installer les plugins recommandés

→ **Référence complète** : [`plugins.md`](plugins.md) — section « Plugins RECOMMANDÉS »

À planifier dans la semaine qui suit l'activation (pas tout d'un coup pour éviter les interactions) :

- [ ] SEO : Yoast SEO **ou** Rank Math (un seul) + sitemap soumis à Google Search Console
- [ ] Anti-spam : Akismet + reCAPTCHA v3 sur CF7
- [ ] Cache : WP Rocket ou Autoptimize
- [ ] Compression images : ShortPixel ou Imagify
- [ ] Sécurité : Wordfence ou Solid Security + 2FA admin
- [ ] Sauvegardes automatiques : UpdraftPlus (DB quotidien + fichiers hebdo)
- [ ] RGPD : Complianz ou Cookie Notice
- [ ] (optionnel) Analytics : Google Site Kit ou Matomo

---

## Étape 8 — Valider ⚠️

→ **Checklist exhaustive** : [`../validation-checklist.md`](../validation-checklist.md)

Les validations minimales à faire avant la bascule publique :

### Pages

- [ ] Homepage : toutes les sections s'affichent (hero, bienvenue, Q&R, overlay, événements, témoignages, newsletter)
- [ ] Chaque page de service (4 pages) : header hero + contenu + CTAs « Me contacter » et « Voir le calendrier »
- [ ] Page Contact : formulaire présent et fonctionnel (test d'envoi réel → vérifier réception email)
- [ ] Page Calendrier : liste d'événements affichée
- [ ] Page À propos : contenu migré correctement

### Accessibilité

- [ ] **axe DevTools** : scanner toutes les pages principales → **0 violation**
- [ ] Navigation clavier complète (Tab, Entrée, Échap, flèches) sur menu, dropdown, carrousel témoignages, formulaires
- [ ] Focus visible partout
- [ ] Skip link fonctionnel (premier Tab sur la homepage)
- [ ] Contrastes : vérifier au WebAIM Contrast Checker les combinaisons critiques

### Performance

- [ ] **Lighthouse** desktop : Performance ≥ 90, Accessibility ≥ 95, Best Practices ≥ 90, SEO ≥ 90
- [ ] Core Web Vitals : LCP < 2.5 s, INP < 200 ms, CLS < 0.1

### Responsive

- [ ] 320 px (iPhone SE) — aucun scroll horizontal
- [ ] 768 px (iPad portrait)
- [ ] 1440 px (desktop)

### Légal / RGPD

- [ ] Mentions légales accessibles depuis le footer (SIRET `910 902 932 00010`, adresse `Le Grand Vron, 86510 Brux`, hébergeur)
- [ ] Bannière cookies fonctionnelle
- [ ] Consentement RGPD explicite sur newsletter + formulaire contact

---

## Étape 9 — Bascule publique

Si le déploiement a été fait sur un environnement de staging (recommandé), basculer sur la production :

1. Copier les fichiers thème + plugins + DB du staging vers la prod (ou suivre la procédure « migration de staging vers prod » de l'hôte).
2. Mettre à jour l'URL `siteurl` et `home` dans la DB si nécessaire (Réglages → Général ou via WP-CLI `wp search-replace`).
3. Vider **tous** les caches :
   - Plugin de cache WordPress
   - Cache serveur (OPcache, Varnish, CDN)
   - Cache navigateur (Ctrl+Shift+R)
4. Tester à nouveau la homepage et les formulaires depuis une session privée non-authentifiée.

Si le déploiement a été fait directement en prod, il suffit de vider les caches.

---

## Étape 10 — Monitoring première semaine

À faire chaque jour pendant 7 jours :

- [ ] Consulter les logs PHP serveur → repérer les erreurs
- [ ] Consulter les soumissions Contact Form 7 (Admin → Flamingo si installé, ou mail direct)
- [ ] Consulter les inscriptions Brevo (côté Brevo)
- [ ] Google Search Console : erreurs d'exploration, couverture, Core Web Vitals
- [ ] Analytics : vérifier qu'il n'y a pas de chute de trafic inattendue

À la fin de la semaine :

- [ ] Relancer Lighthouse sur plusieurs pages pour confirmer l'absence de régression
- [ ] Vérifier que la sauvegarde automatique quotidienne a bien tourné chaque nuit
- [ ] Documenter tout souci rencontré et sa résolution

---

## 🚨 En cas de pépin

→ [`rollback.md`](rollback.md) — diagnostic rapide + 3 niveaux de rollback (admin → SFTP → restauration complète)

Tableau des symptômes les plus fréquents dans [`rollback.md`](rollback.md) (section « Diagnostic rapide »).

---

## ✅ Checklist finale — avant de déclarer le déploiement terminé

Une seule checklist synthétique pour signer le PV de recette :

- [ ] Sauvegarde complète faite, testée, conservée hors serveur
- [ ] Plugins requis installés + configurés (Brevo id=1, Events Manager, CF7)
- [ ] Thème activé sans erreur
- [ ] Menu assigné, logo en place, page d'accueil statique, permaliens OK
- [ ] Tous les templates assignés aux pages
- [ ] Meta fields home + contact remplis
- [ ] Témoignages migrés en CPT
- [ ] Signets migrés vers `[kiyose_signets]`
- [ ] Shortcodes CF7 + Events Manager insérés dans les pages correspondantes
- [ ] Miniatures régénérées
- [ ] Formulaires testés (envoi réel confirmé)
- [ ] Plugins recommandés installés (au minimum : sécurité, cache, SEO, cookies, sauvegardes)
- [ ] Lighthouse Performance ≥ 90 et Accessibility ≥ 95 sur les pages principales
- [ ] axe DevTools : 0 violation
- [ ] Responsive testé 320 → 1920 px
- [ ] RGPD conforme (cookies + consentements)
- [ ] Aucune erreur PHP dans les logs après 1 h de fonctionnement
- [ ] Monitoring J+1 planifié
