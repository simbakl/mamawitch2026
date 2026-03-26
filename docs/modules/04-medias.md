# Module 04 - Médias (Photos, Vidéos, Musique / Discographie)

## Statut : VALIDE

## Réponses aux questions

### Photos
- [x] Galeries organisées par thème (concert, shooting, studio, coulisses...)
- Volume léger, surtout pour habiller le site
- Les réseaux sociaux restent le canal principal pour les photos

### Vidéos
- [x] Hébergées sur YouTube uniquement (pas d'auto-hébergement)
- Section séparée des galeries photos
- Liste chronologique + catégorisation (clips, lives, sessions, interviews...)

### Musique / Discographie
- [x] Module discographie complet (pas juste des liens)
- EP existant + 2 EP en cours + potentiellement un album
- Cette section va s'accélérer dans le temps

### Press kit
- [x] Hors périmètre de ce module — sera traité dans le module espace pro/interne

## Fonctionnalités retenues (MVP)

### 1. Galeries Photos

#### Back-office (Filament)
- CRUD des galeries : titre, description (optionnel), date
- Upload multiple de photos par galerie
- Ordre des photos personnalisable

#### Front public
- Page "Galerie" avec les albums photo par thème
- Clic sur une galerie → affichage des photos en grille
- Lightbox pour visualiser en grand

### 2. Vidéos

#### Back-office (Filament)
- CRUD des vidéos
- Champs :
  - Titre
  - URL YouTube
  - Catégorie (clips officiels, lives, sessions, interviews, autre)
  - Date de publication
- Liste triée par date

#### Front public
- Page "Vidéos" avec les vidéos intégrées (embed YouTube)
- Triées par date (plus récentes en premier)
- Filtre par catégorie

### 3. Discographie

#### Back-office (Filament)
- CRUD des releases
- Champs :
  - Titre
  - Type : EP, Album, Single
  - Cover (upload image)
  - Date de sortie
  - Texte de présentation (optionnel)
  - Player intégré : URL embed Spotify/Bandcamp (optionnel)
  - Liens d'écoute : Spotify, Bandcamp, Apple Music, Deezer, SoundCloud (champs URL optionnels)
  - Crédits : studio, producteur, mixage, mastering, etc. (éditeur riche ou champs structurés)
- CRUD des tracks par release :
  - Titre du morceau
  - Numéro de piste
  - Durée

#### Front public
- Page "Discographie" avec toutes les releases
- Chaque release : cover, titre, type, date de sortie
- Page dédiée par release :
  - Cover grand format
  - Tracklist (titre + durée)
  - Player intégré si renseigné
  - Texte de présentation si renseigné
  - Crédits
  - Boutons vers les plateformes d'écoute

## Fonctionnalités V2
- Galerie photos en lien avec un concert (lier galerie → événement)
- Téléchargement autorisé de certaines photos (presse)
- Intégration SoundCloud player
- Paroles des morceaux
