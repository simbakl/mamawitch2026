<?php

namespace App\Http\Controllers;

use App\Mail\ProInvitationMail;
use App\Models\ProAccount;
use App\Models\ProContentPage;
use App\Models\ProContentType;
use App\Models\ProType;
use App\Models\MusicProject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProController extends Controller
{
    /**
     * Public: access request form
     */
    public function accessRequest()
    {
        $proTypes = ProType::active()->orderBy('sort_order')->get();

        return view('pro.request', compact('proTypes'));
    }

    /**
     * Public: submit access request
     */
    public function accessRequestSubmit(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pro_accounts,email',
            'structure' => 'required|string|max:255',
            'pro_type_id' => 'required|exists:pro_types,id',
            'message' => 'nullable|string|max:2000',
            'honeypot' => 'size:0',
        ]);

        unset($validated['honeypot']);

        ProAccount::create(array_merge($validated, [
            'status' => 'pending',
        ]));

        // Notify admins
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewProAccessRequest($validated));
        }

        return redirect()->route('pro.request')
            ->with('success', 'Votre demande a été envoyée. Vous recevrez un email une fois votre accès validé.');
    }

    /**
     * Public: handle invitation link — create user account and redirect to setup
     */
    public function invitation(string $token)
    {
        $proAccount = ProAccount::where('invitation_token', $token)
            ->where('status', 'invited')
            ->first();

        if (! $proAccount) {
            return redirect()->route('pro.request')
                ->with('error', 'Ce lien d\'invitation est invalide ou a déjà été utilisé.');
        }

        // Token expires after 7 days
        if ($proAccount->invitation_sent_at && $proAccount->invitation_sent_at->addDays(7)->isPast()) {
            return redirect()->route('pro.request')
                ->with('error', 'Ce lien d\'invitation a expiré. Veuillez contacter le groupe pour en obtenir un nouveau.');
        }

        // Check if a user already exists for this email
        $user = User::where('email', $proAccount->email)->first();

        if (! $user) {
            // Create user without password — they'll set it on the setup page
            $user = User::create([
                'name' => $proAccount->full_name,
                'email' => $proAccount->email,
            ]);
            $user->assignRole('pro');
        } elseif (! $user->hasRole('pro')) {
            $user->assignRole('pro');
        }

        // Link pro account to user
        $proAccount->update([
            'user_id' => $user->id,
            'status' => 'approved',
            'approved_at' => $proAccount->approved_at ?? now(),
        ]);

        // Generate setup token and redirect to account setup page
        $setupToken = $user->generateSetupToken();

        return redirect()->route('account.setup', $setupToken);
    }

    /**
     * Authenticated: pro dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        $proAccount = $user->proAccount;

        if (! $proAccount?->proType) {
            abort(403, 'Accès non autorisé.');
        }

        // Get accessible content types for this pro type
        $accessibleContentTypes = $proAccount->proType
            ->contentTypes()
            ->orderBy('sort_order')
            ->get();

        // Load content pages for accessible types
        $contentPages = ProContentPage::with('contentType')
            ->whereIn('pro_content_type_id', $accessibleContentTypes->pluck('id'))
            ->get()
            ->keyBy(fn ($p) => $p->contentType->slug);

        // Get music projects this pro has access to
        $musicProjects = $proAccount->musicProjects()
            ->active()
            ->with('tracks')
            ->orderBy('sort_order')
            ->get();

        return view('pro.dashboard', compact(
            'proAccount',
            'accessibleContentTypes',
            'contentPages',
            'musicProjects',
        ));
    }

    /**
     * Authenticated: view a specific content page
     */
    public function content(string $slug)
    {
        $user = auth()->user();
        $proAccount = $user->proAccount;

        if (! $proAccount?->proType) {
            abort(403, 'Accès non autorisé.');
        }

        $contentType = ProContentType::where('slug', $slug)->firstOrFail();

        // Check access
        $hasAccess = $proAccount->proType->contentTypes()
            ->where('pro_content_types.id', $contentType->id)
            ->exists();

        if (! $hasAccess) {
            abort(403, 'Accès non autorisé à ce contenu.');
        }

        $contentPage = ProContentPage::where('pro_content_type_id', $contentType->id)->first();

        return view('pro.content', compact('contentType', 'contentPage', 'proAccount'));
    }

    /**
     * Authenticated: download a file
     */
    public function downloadFile(string $type, string $filename)
    {
        $user = auth()->user();
        $proAccount = $user->proAccount;

        if (! $proAccount?->proType) {
            abort(403);
        }

        // Prevent path traversal
        if (str_contains($filename, '..') || str_contains($filename, '\\')) {
            abort(400);
        }

        // Check access to this content type
        $contentType = ProContentType::where('slug', $type)->firstOrFail();
        $hasAccess = $proAccount->proType->contentTypes()
            ->where('pro_content_types.id', $contentType->id)
            ->exists();

        if (! $hasAccess) {
            abort(403);
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('pro-files');

        if (! $disk->exists($filename)) {
            abort(404);
        }

        return $disk->download($filename, basename($filename));
    }

    /**
     * Authenticated: download all files as ZIP
     */
    public function downloadZip(string $type)
    {
        $user = auth()->user();
        $proAccount = $user->proAccount;

        if (! $proAccount?->proType) {
            abort(403);
        }

        $contentType = ProContentType::where('slug', $type)->firstOrFail();
        $hasAccess = $proAccount->proType->contentTypes()
            ->where('pro_content_types.id', $contentType->id)
            ->exists();

        if (! $hasAccess) {
            abort(403);
        }

        $contentPage = ProContentPage::where('pro_content_type_id', $contentType->id)->first();
        $files = $contentPage?->files ?? [];

        if (empty($files)) {
            abort(404);
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('pro-files');
        $zipName = 'MamaWitch_' . str_replace('-', '_', $type) . '.zip';
        $zipPath = storage_path('app/private/' . $zipName);

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($files as $file) {
            if ($disk->exists($file)) {
                $zip->addFile($disk->path($file), basename($file));
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Authenticated: view a music project
     */
    public function musicProject(MusicProject $project)
    {
        $user = auth()->user();
        $proAccount = $user->proAccount;

        if (! $proAccount) {
            abort(403);
        }

        // Check access
        if (! $proAccount->musicProjects()->where('music_projects.id', $project->id)->exists()) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $project->load('tracks');

        return view('pro.project', compact('project', 'proAccount'));
    }
}
