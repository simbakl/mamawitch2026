# Module 07 - Rôles & Utilisateurs

## Statut : VALIDE

## Rôles

### Système de rôles cumulables
Un utilisateur peut avoir un ou plusieurs rôles (ex : admin + musicien).

| Rôle | Description | Auth |
|------|-------------|------|
| **Admin** | Accès total sans restriction à tout le back-office | Login + Google optionnel |
| **Éditeur** | Gestion du contenu public uniquement | Login + Google optionnel |
| **Musicien** | Gestion de sa fiche perso, matériel, besoins techniques | Login + Google optionnel |
| **Pro** | Consultation espace pro selon son type + écoute privée | Google SSO uniquement |

### Détail des permissions par rôle

#### Admin
- Tout ce que les autres rôles peuvent faire
- Gestion des utilisateurs (création, rôles, activation, suppression)
- Gestion des comptes pro (validation, invitation, droits)
- Gestion de la matrice d'accès pro
- Attribution des projets musicaux aux pros
- Gestion des types de pros
- Configuration générale du site
- Gestion de l'ordre d'affichage des membres
- Ajout / suppression de membres du groupe

#### Éditeur
- CRUD des concerts
- CRUD des actualités + catégories
- CRUD des galeries photos
- CRUD des vidéos
- CRUD de la discographie (releases + tracks)
- Pas d'accès à : utilisateurs, comptes pro, espace musicien, configuration

#### Musicien
- Modifier sa propre fiche membre (photo, bio, instruments, réseaux)
- CRUD de son propre matériel
- Renseigner ses propres besoins techniques salle
- Voir le matériel et besoins des autres musiciens (lecture seule)
- Accès à l'éditeur de plan de scène
- Générer le PDF fiche technique
- Pas d'accès à : concerts, news, galeries, comptes pro, configuration

#### Pro
- Connexion via Google SSO uniquement
- Dashboard avec contenus filtrés selon son type (salle/booker, presse, label)
- Téléchargement des fichiers autorisés (photos HD, logos, fiche technique)
- Consultation des contenus autorisés (bio, rider, conditions, revue de presse)
- Écoute des projets musicaux attribués individuellement
- Aucun accès au back-office Filament

## Fonctionnalités retenues (MVP)

### Back-office Admin (Filament)
- CRUD des utilisateurs
- Attribution de rôles (multi-sélection)
- Activation / désactivation d'un compte
- Liste des utilisateurs avec filtres par rôle
- Pour les musiciens : lier à une fiche membre existante

### Authentification
- Login classique (email + mot de passe) pour admin, éditeur, musicien
- SSO Google optionnel pour admin, éditeur, musicien (lier son compte Google)
- SSO Google obligatoire pour les pros
- Laravel Socialite (driver Google)
- Middleware de vérification des rôles sur chaque section

### Sécurité
- Hashage des mots de passe (bcrypt, natif Laravel)
- Protection CSRF (natif Laravel)
- Rate limiting sur les tentatives de connexion
- Session sécurisée

## Fonctionnalités V2
- Logs d'activité (qui a fait quoi, quand)
- Permissions granulaires personnalisables par utilisateur
- Double authentification (2FA)
