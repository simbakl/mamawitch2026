# Module 05 - Espace Privé Musiciens

## Statut : VALIDE

## Réponses aux questions

### Objectif du module
- [x] Pas un outil de communication (WhatsApp/Discord restent pour ça)
- [x] Centraliser le matériel de chaque musicien et ses besoins techniques
- [x] Générer automatiquement la fiche technique complète du groupe en PDF

### Planning / Répétitions / Setlists / Communication / Finances
- [x] Hors périmètre — pas de besoin identifié pour cette version

### Matériel
- [x] Chaque musicien gère son propre matériel
- Catégorisé : instrument, ampli, effet/pédale, accessoire
- Chaque élément : nom, catégorie, détail/notes

### Plan de scène
- [x] Éditeur de plan de scène intégré (drag & drop sur une scène virtuelle)
- Le plan généré est inclus dans le PDF

### Besoins techniques salle
- [x] Chaque musicien renseigne ses propres besoins (retours, micros, DI, prises, etc.)
- [x] Section globale complémentaire gérée par l'admin pour ajouter/corriger des besoins oubliés

### Accès
- [x] Tous les membres voient tout et modifient leur propre matériel/besoins
- [x] Tous les membres peuvent générer le PDF
- [x] Pas de hiérarchie de droits entre musiciens

## Fonctionnalités retenues (MVP)

### Back-office - Fiche membre (lien Module 03)

- Chaque musicien peut modifier son propre profil public :
  - Photo, bio courte, instrument(s), réseaux sociaux perso
- L'ordre d'affichage et l'ajout/suppression de membres restent réservés à l'admin

### Back-office - Matériel par musicien

#### Chaque musicien gère :
- CRUD de son matériel
- Champs par élément :
  - Nom (ex : "Gibson Les Paul Standard")
  - Catégorie : instrument, ampli, effet/pédale, accessoire
  - Notes / détails (optionnel, ex : "accordage Drop D", "alimentation 9V")
- Visualisation du matériel des autres membres (lecture seule)

### Back-office - Besoins techniques salle

#### Chaque musicien renseigne :
- Ses besoins en sonorisation (retours, type : bain de pied / side-fill / in-ear)
- Ses besoins en micros / DI
- Ses besoins en électricité (nombre de prises)
- Ses besoins en monitoring (nombre de mixes séparés)
- Divers (risers, tapis, tabourets, autre)

#### Section globale (tout membre peut compléter) :
- Besoins généraux non rattachés à un musicien
- Infos complémentaires (temps de montage/démontage, contraintes particulières)

### Back-office - Plan de scène

- Éditeur visuel intégré (drag & drop)
- Éléments à placer : musiciens, amplis, retours, batterie, micros, DI, etc.
- Scène virtuelle redimensionnable
- Sauvegarde du plan en base de données
- Export image pour le PDF

### Génération PDF - Fiche Technique

Le PDF généré contient :
1. **En-tête** : logo Mama Witch, nom du groupe, contact
2. **Line-up** : liste des musiciens et instruments
3. **Matériel du groupe** : inventaire complet par musicien (ce que le groupe amène)
4. **Plan de scène** : schéma visuel
5. **Besoins techniques** : tableau agrégé de tout ce que la salle doit fournir (par musicien + global)
6. **Infos complémentaires** : temps de montage, contraintes, notes

Accessible à tous les membres, généré en temps réel à partir des données actuelles.

## Fonctionnalités V2
- Historique des versions de la fiche technique
- Gestion de plusieurs configurations (acoustique, électrique, festival, club)
- Planning des répétitions
- Partage de fichiers internes (maquettes, tablatures)
- Setlists
- Gestion financière (caisse commune)
