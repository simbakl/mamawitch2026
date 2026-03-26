# Module 08 - Pages Publiques & Navigation

## Statut : VALIDE

## Réponses aux questions

### Page d'accueil
- [x] Hero éditable depuis l'admin (image/vidéo de fond, texte d'accroche, call-to-action)
- [x] Contenu dynamique alimenté automatiquement par les autres modules

### Contact
- [x] Formulaire de contact classique (nom, email, message) → envoi à contact@mamawitch.fr
- [x] Séparé du formulaire de demande d'accès pro

### Pages statiques
- [x] Mentions légales / CGU (obligatoire)
- [x] Gestionnaire de pages statiques dans l'admin pour le futur
- [x] Pas de page "À propos" (la page membres suffit)

## Fonctionnalités retenues (MVP)

### Back-office Admin (Filament)

#### Gestion du Hero (page d'accueil)
- Image ou vidéo de fond (upload)
- Titre / texte d'accroche
- Bouton call-to-action (texte + lien, optionnel)
- Possibilité de mettre à jour à tout moment

#### Gestionnaire de pages statiques
- CRUD de pages libres
- Champs :
  - Titre
  - Slug (URL de la page)
  - Contenu (éditeur riche)
  - Publié : oui / non
  - Position dans le menu (optionnel)
- Pages par défaut : Mentions légales, CGU

#### Formulaire de contact
- Réception des messages dans le back-office (liste, lu/non lu)
- Notification par email à contact@mamawitch.fr

### Front public

#### Page d'accueil
- **Hero** : image/vidéo plein écran + logo + texte d'accroche + CTA
- **Prochains concerts** : 2-3 prochaines dates (depuis module 01)
- **Dernières actualités** : 2-3 dernières news (depuis module 02)
- **Dernière sortie** : dernier EP/single avec cover et liens d'écoute (depuis module 04)
- **Liens réseaux sociaux**

#### Page Contact
- Formulaire : nom, email, objet, message
- Protection anti-spam (reCAPTCHA ou honeypot)
- Message de confirmation après envoi
- Affichage des liens réseaux sociaux et email

#### Pages statiques
- Pages libres accessibles par leur slug (/mentions-legales, /cgu, etc.)
- Intégrées dans le menu si configuré

#### Navigation
- Menu principal : Accueil, Le Groupe, Concerts, Actualités, Galerie, Vidéos, Discographie, Contact
- Bouton "Espace Pro" (mène au login pro)
- Liens réseaux sociaux dans le footer
- Menu responsive (mobile)

#### SEO
- Balises meta (title, description) par page
- Open Graph (aperçu Facebook, Twitter)
- Sitemap XML auto-généré
- URLs propres (slugs)

## Fonctionnalités V2
- Blog intégré avec commentaires
- Système de bannière promotionnelle (bandeau en haut du site)
- Multi-langue (FR/EN)
- Mode maintenance avec page personnalisée
