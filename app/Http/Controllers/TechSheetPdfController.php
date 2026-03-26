<?php

namespace App\Http\Controllers;

use App\Models\GlobalTechRequirement;
use App\Models\Member;
use App\Models\StagePlan;
use Barryvdh\DomPDF\Facade\Pdf;

class TechSheetPdfController extends Controller
{
    public function generate()
    {
        $members = Member::with(['equipment', 'techRequirement'])->orderBy('sort_order')->get();
        $stagePlan = StagePlan::first();

        $globalRequirements = [
            'setup_time' => GlobalTechRequirement::get('setup_time'),
            'soundcheck_time' => GlobalTechRequirement::get('soundcheck_time'),
            'teardown_time' => GlobalTechRequirement::get('teardown_time'),
            'global_monitors' => GlobalTechRequirement::get('global_monitors'),
            'global_other' => GlobalTechRequirement::get('global_other'),
            'global_notes' => GlobalTechRequirement::get('global_notes'),
        ];

        $pdf = Pdf::loadView('pdf.tech-sheet', [
            'members' => $members,
            'stagePlan' => $stagePlan,
            'stagePlanElements' => $stagePlan?->elements ?? [],
            'global' => $globalRequirements,
        ]);

        $pdf->setPaper('A4');

        return $pdf->download('Mama_Witch_Fiche_Technique.pdf');
    }
}
