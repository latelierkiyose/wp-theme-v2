# Plugins WordPress pour la mise en production

Ce document liste tous les plugins à installer pour que le thème « L'Atelier Kiyose » fonctionne correctement en production, avec une séparation nette entre ce qui est **indispensable** et ce qui est **recommandé**.

> 🔗 Revenir à la procédure principale → [`README.md`](README.md)

---

## ⚠️ Plugins REQUIS — le site ne fonctionne pas sans eux

Trois plugins sont consommés par le thème ou par le contenu existant. Leur absence casse des pages publiquement visibles.

### 1. Brevo (newsletter)

| | |
|---|---|
| **Nom officiel** | Brevo (anciennement Sendinblue) |
| **Slug** | `mailin` |
| **Installation** | Extensions → Ajouter → rechercher « Brevo » → Installer → Activer |
| **Utilisé par le thème** | `latelierkiyose/footer.php` (bloc newsletter du footer, présent sur chaque page) et `latelierkiyose/template-parts/home/overlays.php` (overlay newsletter de la home) |
| **Shortcode utilisé** | `[sibwp_form id=1]` ⚠️ **pas** `[brevo_form]` |
| **Comportement si absent** | Les blocs newsletter disparaissent silencieusement (le thème vérifie `shortcode_exists()` avant d'afficher). Pas de texte cassé visible, mais plus aucun moyen de s'inscrire. |

**Configuration minimale obligatoire** :

1. Connecter un compte Brevo (créer un compte gratuit si besoin sur https://www.brevo.com)
2. Dans l'admin WP : Brevo → Formulaires → Créer un formulaire
3. Vérifier que l'ID du formulaire créé est **exactement `1`** (sinon le shortcode du thème ne l'affichera pas). Si l'ID diffère, voir « Que faire si l'ID n'est pas 1 » plus bas.
4. Activer le **double opt-in RGPD** dans les paramètres du formulaire
5. Champs minimum : Email (requis)
6. Tester l'inscription depuis le site

**Que faire si l'ID n'est pas 1** : deux options.
- Créer dans Brevo un nouveau formulaire (le premier aura souvent l'ID 1 s'il n'y en a jamais eu d'autres) et supprimer l'ancien.
- Ou modifier le thème pour pointer vers le bon ID dans les deux emplacements cités ci-dessus (nécessite une intervention développeur). Dans ce cas, penser à mettre à jour ce document.

---

### 2. Events Manager (calendrier)

| | |
|---|---|
| **Nom officiel** | Events Manager |
| **Slug** | `events-manager` |
| **Installation** | Extensions → Ajouter → rechercher « Events Manager » → Installer → Activer |
| **Utilisé par le thème** | `latelierkiyose/template-parts/home/events.php` — shortcode **hardcodé** dans la section « Prochains événements » |
| **Shortcode utilisé** | `[events_list limit="4" scope="future" orderby="event_start_date" order="ASC"]` |
| **Comportement si absent** | La section homepage affiche un message temporaire au lieu des dates réelles. Pas de shortcode brut visible, mais le plugin reste requis pour la mise en production. |

**Configuration minimale obligatoire** :

1. Événements → Réglages : vérifier le fuseau horaire et les formats de date
2. Créer au moins **1 événement à venir** (sinon la section homepage est vide, mais pas cassée)
3. Vérifier le rendu de `[events_list]` dans un brouillon avant de publier
4. La page « Calendrier & Tarifs » (template `page-calendar.php`) utilise `the_content()` : la personne qui migre le contenu doit insérer un shortcode Events Manager plus complet dans le corps de la page (ex. `[events_list limit="20" scope="future"]`).

---

### 3. Contact Form 7 (formulaire de contact)

| | |
|---|---|
| **Nom officiel** | Contact Form 7 |
| **Slug** | `contact-form-7` |
| **Installation** | Extensions → Ajouter → rechercher « Contact Form 7 » → Installer → Activer |
| **Utilisé par le thème** | Indirectement via `the_content()` dans `latelierkiyose/templates/page-contact.php:31`. Le formulaire est inséré dans le **contenu de la page Contact**, pas dans le template. |
| **Shortcode utilisé** | `[contact-form-7 id="XXX" title="..."]` (ID généré à la création du formulaire) |
| **Comportement si absent** | 🛑 Le shortcode présent dans le contenu de la page Contact actuelle s'affiche en texte brut. |

**Configuration minimale obligatoire** :

1. Contact → Formulaires de contact → créer ou conserver le formulaire existant (nom, email, message, sujet)
2. Ajouter explicitement une case à cocher de **consentement RGPD** avant le bouton d'envoi
3. Configurer l'email de destination dans l'onglet « E-mail »
4. Insérer le shortcode renvoyé par CF7 (ex. `[contact-form-7 id="123" title="Contact"]`) dans le contenu de la page Contact via Gutenberg → bloc « Shortcode »
5. Tester l'envoi d'un message et vérifier la réception côté boîte mail

**Anti-spam** : coupler avec **reCAPTCHA v3** (voir section recommandée ci-dessous) — le site recevra du spam sans protection.

---

## ✅ Plugins RECOMMANDÉS — à installer après l'activation du thème

Ces plugins ne sont pas requis pour que le thème fonctionne, mais sont fortement recommandés pour une mise en production saine.

### Anti-spam

- **Akismet Anti-Spam** — filtre les commentaires et soumissions de formulaires. Inclus d'office dans WordPress, il suffit de l'activer et d'obtenir une clé API (gratuite pour usage personnel).
- **reCAPTCHA v3 (invisible)** via Contact Form 7 — configuration : Contact → Intégration → reCAPTCHA, puis ajouter les balises `[recaptcha]` dans le formulaire (ou s'appuyer sur l'intégration native invisible).

### SEO

Le thème n'injecte **aucune** balise SEO custom (ni schema.org, ni Open Graph). Un plugin SEO est nécessaire.

- **Yoast SEO** ou **Rank Math** — installer **un seul des deux**, jamais les deux en parallèle.
  - Configurer les meta title/description par défaut
  - Générer et soumettre le sitemap XML à Google Search Console
  - Vérifier que le schema `LocalBusiness` est bien renseigné (adresse, horaires)

### Performance / cache

Les assets CSS/JS du thème sont déjà minifiés au build (`npm run build`), mais un cache de pages reste utile.

- **WP Rocket** (payant, premium) ou **Autoptimize** (gratuit) ou **W3 Total Cache** (gratuit)
  - Activer le cache des pages
  - Activer le lazy loading natif WordPress si absent
  - Vider le cache après chaque mise à jour

### Compression images

Les tailles `kiyose-hero` (1920×800) peuvent être lourdes. Un plugin de compression les convertit en WebP à la volée.

- **ShortPixel** ou **Imagify** (payants avec quota gratuit mensuel) ou **Smush** (gratuit basique)
  - Activer la conversion WebP avec repli automatique
  - Lancer une optimisation en masse sur les médias existants après la migration

### Sécurité

- **Wordfence Security** ou **Solid Security** (anciennement iThemes Security) — pare-feu, scan malware, limitation des tentatives de connexion
- **Authentification 2FA** — intégrée dans Wordfence/Solid, ou plugin indépendant (« Two Factor Authentication »)
- Forcer le HTTPS (souvent géré par l'hébergeur) et vérifier l'absence de contenu mixte

### Sauvegardes automatiques

- **UpdraftPlus** (recommandé) ou **BackWPup**
  - Planifier : sauvegarde DB quotidienne + sauvegarde fichiers hebdomadaire
  - Envoyer les archives vers un stockage externe (Google Drive, Dropbox, S3…)
  - Tester une restauration sur un environnement de test au moins une fois

### RGPD / cookies

- **Complianz** (le plus complet) ou **Cookie Notice & Compliance**
  - Bannière de consentement cookies
  - Scan automatique des cookies déposés
  - Mentions légales / politique de confidentialité à maintenir à jour manuellement

### Analytics (optionnel)

- **Google Site Kit** — intègre Google Analytics 4 et Search Console dans l'admin WP
- **Matomo** — alternative auto-hébergée respectueuse de la vie privée

### Utilitaire ponctuel après migration

- **Regenerate Thumbnails** — à installer et exécuter **une seule fois** juste après l'activation du thème, pour générer les nouvelles tailles `kiyose-hero` (1920×800) et `kiyose-testimony-thumb` (150×150) sur les médias existants. Désinstaller ensuite.

---

## 🚫 Plugins à NE PAS installer

Parce qu'ils dupliquent une fonctionnalité déjà fournie par le thème ou dégradent les performances :

- **Plugins de témoignages tiers** (Strong Testimonials, etc.) — le thème fournit le CPT `kiyose_testimony` et le shortcode `[kiyose_testimonials]`. Voir [`content-migration.md`](content-migration.md) — section « Témoignages ».
- **Plugins d'ancres automatiques pour titres** — déjà fait par `latelierkiyose/inc/content-filters.php` (génération automatique d'IDs sur les H2, alimente `[kiyose_signets]`).
- **Plugins de blocs Gutenberg supplémentaires (Kadence Blocks, Stackable…)** — le thème repose sur les blocs natifs et ses classes CSS dédiées (`doc/user/03-mise-en-page-avec-des-classes.md`).
- **Plugins qui rechargent jQuery** sur toutes les pages — le thème est en vanilla JS, et réintroduire jQuery partout dégrade les performances et l'accessibilité clavier (conflits possibles).
- **Plugins de page builder** (Elementor, Divi…) — incompatibles avec l'approche « template PHP classique ».

---

## Récapitulatif — checklist à cocher

Avant d'activer le thème sur la production :

- [ ] Brevo installé, activé, formulaire d'ID 1 créé
- [ ] Events Manager installé, activé, au moins 1 événement créé
- [ ] Contact Form 7 installé, activé, formulaire existant conservé ou recréé avec consentement RGPD

Dans la semaine qui suit l'activation :

- [ ] Plugin SEO configuré + sitemap soumis à Google Search Console
- [ ] Plugin de cache installé
- [ ] Plugin de compression d'images lancé en masse
- [ ] Plugin de sécurité + 2FA actifs
- [ ] Sauvegardes automatiques planifiées et testées
- [ ] Bannière cookies en place
- [ ] Regenerate Thumbnails exécuté puis désinstallé
