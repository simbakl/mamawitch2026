<?php

namespace Database\Seeders;

use App\Models\ProContentType;
use App\Models\ProContentPage;
use App\Models\ProType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProContentSeeder extends Seeder
{
    public function run(): void
    {
        // Create pro types
        $salleBooker = ProType::firstOrCreate(
            ['slug' => 'salle-booker'],
            ['name' => 'Salle / Booker', 'description' => 'Programmateurs, tourneurs, organisateurs', 'sort_order' => 1]
        );

        $presse = ProType::firstOrCreate(
            ['slug' => 'presse'],
            ['name' => 'Presse', 'description' => 'Journalistes, webzines, radios, blogs', 'sort_order' => 2]
        );

        $label = ProType::firstOrCreate(
            ['slug' => 'label'],
            ['name' => 'Label', 'description' => 'Labels, managers externes', 'sort_order' => 3]
        );

        // Create content types
        $contentTypes = [
            ['name' => 'Fiche technique', 'slug' => 'fiche-technique', 'sort_order' => 1],
            ['name' => 'Plan de scène', 'slug' => 'plan-de-scene', 'sort_order' => 2],
            ['name' => 'Hospitality rider', 'slug' => 'hospitality-rider', 'sort_order' => 3],
            ['name' => 'Photos HD', 'slug' => 'photos-hd', 'sort_order' => 4],
            ['name' => 'Logos vectoriels', 'slug' => 'logos-vectoriels', 'sort_order' => 5],
            ['name' => 'Bio longue presse', 'slug' => 'bio-longue-presse', 'sort_order' => 6],
            ['name' => 'Revue de presse', 'slug' => 'revue-de-presse', 'sort_order' => 7],
            ['name' => 'Conditions de booking', 'slug' => 'conditions-booking', 'sort_order' => 8],
            ['name' => 'Contact booking direct', 'slug' => 'contact-booking-direct', 'sort_order' => 9],
        ];

        foreach ($contentTypes as $ct) {
            $type = ProContentType::firstOrCreate(['slug' => $ct['slug']], $ct);

            // Create empty content page for each type
            ProContentPage::firstOrCreate(['pro_content_type_id' => $type->id]);
        }

        // Access matrix per spec:
        // Salle/Booker: fiche-technique, plan-de-scene, hospitality-rider, photos-hd, logos-vectoriels, conditions-booking, contact-booking-direct
        // Presse: photos-hd, logos-vectoriels, bio-longue-presse, revue-de-presse
        // Label: photos-hd, logos-vectoriels, bio-longue-presse, revue-de-presse, conditions-booking, contact-booking-direct

        $matrix = [
            $salleBooker->id => ['fiche-technique', 'plan-de-scene', 'hospitality-rider', 'photos-hd', 'logos-vectoriels', 'conditions-booking', 'contact-booking-direct'],
            $presse->id => ['photos-hd', 'logos-vectoriels', 'bio-longue-presse', 'revue-de-presse'],
            $label->id => ['photos-hd', 'logos-vectoriels', 'bio-longue-presse', 'revue-de-presse', 'conditions-booking', 'contact-booking-direct'],
        ];

        foreach ($matrix as $proTypeId => $slugs) {
            $contentTypeIds = ProContentType::whereIn('slug', $slugs)->pluck('id');
            ProType::find($proTypeId)->contentTypes()->syncWithoutDetaching($contentTypeIds);
        }
    }
}
