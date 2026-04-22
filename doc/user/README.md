# Documentation pour la rédaction

Cette documentation te guide pour rédiger du contenu sur **latelierkiyose.fr**. Pas besoin de savoir coder : tout se fait depuis l'interface WordPress.

## Par où commencer ?

Trois parcours, au choix :

### 🌱 Je découvre WordPress
Tu n'as jamais utilisé cette interface. Suis les étapes dans l'ordre :
1. [Premiers pas dans WordPress](01-premiers-pas.md)
2. [Choisir un template de page](02-choisir-un-template.md)
3. [Utiliser les shortcodes](04-shortcodes.md)

### 🧩 Je cherche comment faire X
Tu veux accomplir une tâche précise. Va dans le dossier **[recettes](recettes/)** :
- [Ajouter un témoignage](recettes/ajouter-un-temoignage.md)
- [Créer une page de service](recettes/creer-une-page-de-service.md)
- [Créer une section en colonnes](recettes/creer-une-section-en-colonnes.md)
- [Ajouter un sommaire automatique](recettes/ajouter-un-sommaire-automatique.md)
- [Mettre en avant une information](recettes/mettre-en-avant-une-information.md)
- [Modifier la page d'accueil](recettes/modifier-la-page-d-accueil.md)

### 📖 J'ai besoin d'une référence
Tu veux la liste exhaustive d'un élément :
- [Templates de page](02-choisir-un-template.md) — quel modèle pour quelle page
- [Classes de mise en page](03-mise-en-page-avec-des-classes.md) — conteneurs, colonnes, sections
- [Shortcodes](04-shortcodes.md) — `[kiyose_testimonials]`, `[kiyose_signets]`, `[kiyose_callout]`
- [Témoignages](05-temoignages.md) — gérer le carnet de témoignages
- [Événements et calendrier](06-evenements-calendrier.md) — Events Manager
- [Formulaires et newsletter](07-formulaires-et-newsletter.md) — Contact Form 7 + Brevo
- [Glossaire](glossaire.md) — définitions des termes techniques
- [À venir](a-venir.md) — fonctionnalités en cours de préparation

## Démarrage rapide (5 étapes)

1. **Se connecter** : ouvre `https://latelierkiyose.fr/wp-admin` et entre tes identifiants.
2. **Créer une page** : dans le menu de gauche, va dans **Pages** puis **Ajouter**.
3. **Choisir un template** : dans le panneau de droite, onglet **Page**, section **Modèle**, sélectionne le template adapté (par exemple **Page de service**).
4. **Écrire le contenu** : l'éditeur central est **Gutenberg**. Tape du texte, ajoute des blocs avec le bouton **+**, colle des shortcodes là où c'est utile.
5. **Publier** : clique sur **Publier** en haut à droite.

C'est tout. Le reste de cette documentation détaille chaque étape.

## Conventions utilisées dans cette doc

| Convention | Usage |
|---|---|
| `code inline` | Ce que tu **tapes ou colles** : `[kiyose_callout]`, `container`, `kiyose_testimony` |
| **Gras** | Ce que tu **vois** dans WordPress : **Publier**, **Ajouter un bloc**, **Modèle** |
| « Guillemets français » | Texte rédigé pour le visiteur du site |
| *Italique* | Premier emploi d'un terme technique (avec sa définition à la suite) |

**Un mot que tu ne connais pas ?** → va voir le [glossaire](glossaire.md).

## Cette doc évolue avec le thème

Les shortcodes, templates et classes changent parfois. Cette documentation est mise à jour en même temps que le code : elle reste donc à jour.

**Si quelque chose ne correspond plus à ce que tu vois**, signale-le à ton développeur — il corrigera la doc.
