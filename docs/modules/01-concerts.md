# Module 01 - Gestion des Concerts

## Statut : VALIDE

## Réponses aux questions

### Volume & Fréquence
- [x] ~10 concerts/an actuellement, amené à évoluer à la hausse
- Prévoir un système flexible et scalable

### Billetterie
- [x] Pas de billetterie interne
- Lien externe vers la plateforme de vente (Shotgun, Dice, Billetweb...)
- Possibilité d'indiquer "complet"

### Rider Technique
- [x] Hors périmètre de ce module — sera traité dans un module dédié (espace pro/interne)

### Types d'événements
- [x] Champ "type" visible uniquement en back-office (concert, festival, release party, showcase, autre)
- Pas de filtre par type côté public

### Statuts
- [x] Pas de workflow interne (pas de "en négociation")
- 3 états manuels : **à venir** (défaut), **complet**, **annulé**
- 1 état automatique : **passé** (quand la date est dépassée)
- Seuls les événements publiés apparaissent sur le site

## Fonctionnalités retenues (MVP)

### Back-office (Filament)
- CRUD complet des événements
- Champs :
  - Titre de l'événement
  - Date et heure
  - Lieu (nom + ville)
  - Lien billetterie externe (optionnel)
  - Type d'événement (interne, non public) : concert, festival, release party, showcase, autre
  - Affiche / image (upload)
  - Description / notes (optionnel)
  - Statut : à venir / complet / annulé
  - Publié : oui / non
- Liste triée par date (prochains en premier)
- Filtres par statut, par type

### Front public
- Liste des prochains concerts (statut "à venir")
- Affichage : date, lieu, ville, lien billetterie (bouton)
- Badge visuel "COMPLET" ou "ANNULÉ" si applicable
- Les concerts passés restent visibles (section séparée ou archive)
- Pas de filtre côté public

## Fonctionnalités V2
- Calendrier visuel
- Export iCal / Google Calendar
- Carte interactive des lieux
- Intégration réseaux sociaux (partage auto d'une date)
