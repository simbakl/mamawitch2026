# Module 02 - Actualités / Communication

## Statut : VALIDE

## Réponses aux questions

### Format de contenu
- [x] Système hybride : annonces courtes par défaut + page détaillée optionnelle
- Titre + texte court toujours visible sur le listing
- Contenu détaillé optionnel → si rempli, bouton "Lire la suite" vers page dédiée

### Canaux actuels
- [x] Communication uniquement via réseaux sociaux aujourd'hui
- Le site vient compléter les réseaux, pas les remplacer

### Programmation
- [x] Oui, besoin de programmer la publication à une date/heure précise
- Champ `published_at` : si date future → publication automatique à l'heure prévue

### Newsletter
- [x] Pas de newsletter pour cette version
- À reconsidérer en V2 si le besoin émerge

### Catégorisation
- [x] Oui, système de catégories éditable depuis l'admin
- Exemples : concert, sortie, média, groupe (modifiable par l'admin)
- Filtrage possible côté public

### Médias
- [x] Image à la une (upload)
- [x] Intégration vidéo YouTube dans le corps de la news
- Pas de player Spotify/Bandcamp intégré pour l'instant

## Fonctionnalités retenues (MVP)

### Back-office (Filament)
- CRUD complet des actualités
- Champs :
  - Titre
  - Texte court / résumé (affiché sur le listing)
  - Contenu détaillé (éditeur riche, optionnel) → génère une page dédiée
  - Image à la une (upload)
  - Vidéo YouTube (URL, optionnel)
  - Catégorie (relation vers catégories éditables)
  - Date de publication programmée (`published_at`)
  - Publié : oui / non
- Liste triée par date (plus récentes en premier)
- Filtres par catégorie, par statut de publication

### Gestion des catégories
- CRUD des catégories de news (nom, slug)
- Libre création par l'admin

### Front public
- Fil d'actualités chronologique
- Chaque news : image, titre, résumé, catégorie, date
- Bouton "Lire la suite" si contenu détaillé présent
- Page article dédiée avec contenu riche + vidéo YouTube intégrée
- Filtre par catégorie

## Fonctionnalités V2
- Newsletter (inscription + envoi automatique ou manuel)
- Partage automatique vers réseaux sociaux à la publication
- Intégration player Spotify / Bandcamp dans les news
