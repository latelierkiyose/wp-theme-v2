# Plugins & Intégrations

## Calendrier & Réservations (En place)

**Events Manager** ✅
- Plugin déjà utilisé pour la gestion des événements et réservations
- Pas de paiement en ligne (réservation uniquement)

**Intégration thème:**
- Shortcode Events Manager dans `templates/page-calendar.php`
- Widget homepage pour afficher les prochains événements
- Personnalisation CSS pour matcher le design du thème
- Templates Events Manager custom dans `plugins/events-manager/` (relatif au thème) si nécessaire

## Newsletter (En place)

**Brevo** (anciennement Sendinblue) ✅
- Plugin déjà utilisé pour la newsletter
- Formulaire intégré via **shortcode du plugin Brevo** dans footer + homepage
- Champs: Email (requis), Prénom (requis), Nom (optionnel)
- Double opt-in RGPD conforme
- Styling custom via CSS BEM (override des classes Brevo par défaut)

## Formulaires Contact

**Contact Form 7** ✅
- Formulaire contact personnalisé via shortcode
- Protection anti-spam (honeypot + reCAPTCHA v3)
- Validation accessible (messages d'erreur ARIA)
- Styling custom via CSS BEM (override des classes CF7 par défaut)

## SEO & Performance

**SEO:**
- **Yoast SEO** ou **Rank Math**
- Métadonnées optimisées
- Sitemap XML
- Schema.org markup

**Performance:**
- **WP Rocket** (recommandé) ou **Autoptimize** (gratuit)
- Minification CSS/JS/HTML
- Cache
- Lazy loading
- **ShortPixel** ou **Imagify** - Compression images automatique

## Sécurité

**Wordfence Security** ou **iThemes Security**
- Protection contre attaques
- Firewall
- Scan malware
- 2FA pour admin

## Réseaux sociaux

**Custom implementation recommandée**
- Liens footer: Facebook, Instagram, LinkedIn (@latelierkiyose)
- SVG icons optimisés
- Accessible (`aria-label`)

## Backup

**UpdraftPlus** ou **BackWPup**
- Sauvegardes automatiques
- Stockage cloud
- Restauration facile

## Configuration WordPress recommandée

**Permaliens:** `/%postname%/` (SEO-friendly)

**Lecture:**
- Page d'accueil statique
- Page blog dédiée pour actualités

## Intégrations tierces

**reCAPTCHA:** v3 (invisible) pour formulaires

**RGPD:**
- **Complianz** ou **Cookie Notice** plugin
- Bannière cookies
- Politique de confidentialité

## Notes importantes

- ⚠️ **Limiter le nombre de plugins** - Chaque plugin ajoute du poids et des risques de sécurité
- ⚠️ **Vérifier la compatibilité** - Tous les plugins doivent être compatibles avec WordPress 6.7+
- ⚠️ **Mises à jour régulières** - Maintenir tous les plugins à jour pour sécurité et performance
- ⚠️ **Tests après installation** - Tester accessibilité et performance après chaque ajout de plugin
