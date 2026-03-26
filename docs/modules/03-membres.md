# Module 03 - Gestion des Membres du groupe

## Statut : VALIDE

## Réponses aux questions

### Effectif
- [x] 5 membres actuellement

### Line-up
- [x] Pas d'historique des anciens membres
- Ajout / suppression manuelle dans l'admin si le line-up évolue

### Profil public
- [x] Chaque membre a un profil public avec :
  - Nom / pseudo
  - Photo
  - Instrument(s)
  - Bio courte
  - Liens réseaux sociaux perso (optionnel, au choix du membre)

### Autres projets
- [x] Pas de section dédiée aux autres projets

### Ordre d'affichage
- [x] Ordre personnalisable dans l'admin (drag & drop / champ position)

## Fonctionnalités retenues (MVP)

### Back-office Admin (Filament)
- CRUD complet des membres
- Champs :
  - Nom / pseudo
  - Photo (upload)
  - Instrument(s)
  - Bio courte (textarea)
  - Réseaux sociaux perso (optionnel) : Instagram, Facebook, X, YouTube, autre
  - Ordre d'affichage (drag & drop ou champ position)
- Liste ordonnée par position

### Espace Privé Musicien
- Chaque musicien peut modifier sa propre fiche membre :
  - Photo, bio courte, instrument(s), réseaux sociaux perso
- Les modifications sont visibles en temps réel sur le site public
- L'ordre d'affichage reste géré par l'admin uniquement

### Front public
- Page "Le groupe" avec les membres affichés dans l'ordre défini en admin
- Chaque membre : photo, nom/pseudo, instrument(s), bio courte
- Icônes cliquables vers les réseaux perso si renseignés

## Fonctionnalités V2
- Galerie photos individuelle par membre
- Lien vers autres projets musicaux
