# Mama Witch 2026 - Site Web Officiel

## Projet

Site internet pour le groupe de Hard Rock **Mama Witch** (Paris, France).
Influences : Metallica, Machine Head, Korn. EP sorti en 2017 (7 titres).

Site existant : https://mamawitch.fr (landing page minimaliste - refonte complète)

## Stack Technique

| Composant | Technologie | Version |
|-----------|-------------|---------|
| Langage | PHP | 8.2+ (serveur upgradé) |
| Framework | Laravel | 12.x |
| Panel Admin | Filament | 3.x |
| Frontend | Blade + Livewire + Tailwind CSS | - |
| Base de données | MySQL / MariaDB | Via WAMP |
| Serveur local | WAMP | Windows |

## Architecture

### Niveaux d'accès (3 zones)

1. **Public** - Accessible à tous
   - Page d'accueil (hero, actualités, prochains concerts)
   - Biographie du groupe et membres
   - Discographie (EP, singles, avec players intégrés)
   - Dates de concerts / événements
   - Galerie photos / vidéos
   - Liens réseaux sociaux et plateformes de streaming
   - Contact / Presse (EPK)

2. **Semi-privé** (Espace Pro) - Accès sur demande/invitation
   - Kit presse téléchargeable (photos HD, logos, rider technique)
   - Documents techniques (fiche technique, rider)
   - Partage de fichiers entre professionnels
   - Informations de booking

3. **Privé** (Back-office Musiciens) - Réservé aux membres du groupe
   - Gestion des répétitions (planning, notes)
   - Partage de fichiers internes (maquettes, tablatures, partitions)
   - Setlists et gestion des morceaux
   - Communication interne (notes, messages)
   - Gestion financière basique (cagnotte, dépenses)

### Modules Admin (Filament)

- **Gestion des concerts** : CRUD dates, lieux, billets, affiches
- **Gestion des actualités** : blog/news avec éditeur riche
- **Gestion des médias** : upload photos, vidéos, audio
- **Gestion des membres** : profils, rôles, instruments
- **Gestion des utilisateurs** : rôles (admin, musicien, pro, public)
- **Gestion du contenu** : pages statiques éditables
- **SEO** : meta tags, sitemap, Open Graph

## Réseaux sociaux du groupe

| Plateforme | Lien |
|------------|------|
| Facebook | facebook.com/littlemamawitch |
| Instagram | @mamawitch_music |
| Twitter/X | @mamawitchoff |
| YouTube | bit.ly/2BCf54W |
| Bandcamp | mamawitch.bandcamp.com |
| Spotify | spoti.fi/2Ael7Ya |
| Apple Music | apple.co/2QU8j3W |
| Deezer | bit.ly/2LwnMks |
| SoundCloud | soundcloud.com/mamawitch |
| Email | contact@mamawitch.fr |

## Identité visuelle

- **Ambiance** : Sombre, heavy, rock - fond sombre, typographie claire
- **Logo** : MW_Logo_typo_white_V2.png (blanc sur fond sombre)
- **Couleurs** : Palette sombre avec accents (à définir - rouge/orange/violet ?)
- **Typographie** : Heavy, bold, style rock/metal

## Conventions de développement

- **Langue du code** : Anglais (variables, fonctions, classes)
- **Langue du contenu** : Français
- **Commits** : Messages en anglais, conventionnels (feat:, fix:, chore:)
- **Architecture** : Respect des conventions Laravel (PSR-4, PSR-12)
- **Tests** : PHPUnit / Pest pour les tests
- **Qualité** : Laravel Pint pour le formatage du code

## Chemin du projet

```
c:\wamp64\www\MamaWitch2026\
```

## Déploiement

- Serveur de production : PHP 8.2+, MySQL
- Domaine : mamawitch.fr
- Environnement local : WAMP64 (Windows)
