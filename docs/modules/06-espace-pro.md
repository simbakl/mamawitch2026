# Module 06 - Espace Semi-Privé (Pro)

## Statut : VALIDE

## Réponses aux questions

### Public cible
- [x] 3 types de professionnels :
  - **Salle / Booker** : programmateurs, tourneurs, organisateurs
  - **Presse** : journalistes, webzines, radios, blogs
  - **Label** : labels, managers externes

### Accès
- [x] Deux modes d'accès :
  - **Demande publique** : bouton "Accès Pro" sur le site → formulaire (nom, structure, type, email) → validation manuelle par l'admin
  - **Invitation** : l'admin génère un lien unique envoyé au pro → le pro crée son compte via ce lien
- [x] **Authentification SSO Google uniquement** pour les pros (pas de login/mot de passe)

### Contenu
- [x] Contenu éditable en ligne depuis l'admin (pas de PDF uploadés sauf fiche technique auto-générée)
- [x] Droits d'accès dynamiques et administrables (pas codés en dur)

### Matrice d'accès par défaut

| Contenu | Salle/Booker | Presse | Label |
|---------|:---:|:---:|:---:|
| Fiche technique (PDF auto-généré module 05) | oui | non | non |
| Plan de scène | oui | non | non |
| Hospitality rider | oui | non | non |
| Photos HD téléchargeables | oui | oui | oui |
| Logos vectoriels | oui | oui | oui |
| Bio longue presse | non | oui | oui |
| Revue de presse | non | oui | oui |
| Conditions de booking | oui | non | oui |
| Contact booking direct | oui | non | oui |
| Écoute privée (projets musicaux) | attribution individuelle par l'admin |

Cette matrice est modifiable par l'admin depuis le back-office.

### Écoute privée sécurisée
- [x] Lecteur audio sécurisé pour partager des titres inédits (démos, maquettes)
- [x] Pas de téléchargement possible
- [x] Accès par projet, attribué individuellement par pro (pas par type)
- [x] Pas de watermarking
- [x] Pas de limite de temps ou d'écoutes

### Authentification
- [x] Pros : SSO Google uniquement
- [x] Admin + musiciens : login/mot de passe + possibilité de lier un compte Google

## Fonctionnalités retenues (MVP)

### Back-office Admin (Filament)

#### Gestion des types de pros
- CRUD des types (salle/booker, presse, label + possibilité d'en ajouter)
- Matrice de droits éditable : pour chaque type, cocher les contenus accessibles

#### Gestion des comptes pro
- Liste des comptes pro (nom, structure, type, email, date de création)
- Validation / refus des demandes d'accès
- Génération de liens d'invitation uniques
- Activation / désactivation d'un compte

#### Gestion des contenus pro
- **Fiche technique** : lien vers le PDF auto-généré (module 05)
- **Plan de scène** : lien vers le plan du module 05
- **Hospitality rider** : éditeur riche (besoins loges, boissons, repas, serviettes...)
- **Photos HD** : upload multiple, téléchargeables par les pros autorisés
- **Logos vectoriels** : upload (PNG, SVG, AI...), téléchargeables
- **Bio longue presse** : éditeur riche
- **Revue de presse** : liste éditable (titre article, URL, nom du média, date)
- **Conditions de booking** : éditeur riche (cachet, défraiements, déplacements, hébergement...)
- **Contact booking direct** : champs texte (nom, téléphone, email)

#### Écoute privée sécurisée
- CRUD des projets musicaux (ex : "EP2 - Work in progress")
  - Titre du projet
  - Description (optionnel)
  - Upload des fichiers audio (stockés hors du dossier public)
- CRUD des pistes par projet :
  - Titre du morceau
  - Fichier audio (MP3/WAV)
  - Ordre dans le projet
- Attribution d'accès : sélection individuelle des pros autorisés par projet

#### Sécurité audio
- Fichiers audio stockés hors du dossier public (non accessibles par URL directe)
- Streaming via API sécurisée avec URLs signées temporaires
- Pas de balise `<audio>` standard avec source exposée
- Blocage clic droit / inspection du code source
- Headers HTTP interdisant le cache navigateur
- Player custom intégré (lecture seule, pas de téléchargement)

### Front - Accès Pro

#### Page publique
- Bouton "Espace Pro" visible sur le site
- Formulaire de demande d'accès : nom, prénom, structure/média, type de profil, email Google, message
- Connexion via **Google SSO uniquement**
- Message de confirmation après envoi de demande

#### Espace Pro (authentifié)
- Dashboard pro avec accès uniquement aux contenus autorisés selon le type de compte
- Téléchargement des fichiers (photos HD, logos, fiche technique PDF)
- Consultation des contenus éditoriaux (bio, rider, conditions, revue de presse)
- **Lecteur sécurisé** : accès aux projets musicaux attribués individuellement
  - Player custom : lecture, pause, barre de progression
  - Affichage : titre du projet, liste des pistes
  - Aucune option de téléchargement

### Authentification (transversal)
- **Laravel Socialite** pour SSO Google
- Pros : connexion Google obligatoire, email Google vérifié à l'inscription
- Admin / Musiciens : login classique (email + mot de passe) avec option de lier un compte Google pour connexion rapide

## Fonctionnalités V2
- Statistiques de consultation (qui a téléchargé quoi, qui a écouté quoi)
- Expiration automatique des comptes pro (accès temporaire)
- Notifications admin quand un pro télécharge la fiche technique
- Espace de messages direct pro ↔ groupe
- Watermarking audio si besoin de traçabilité
- Limitation du nombre d'écoutes par titre
