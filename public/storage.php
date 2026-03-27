<?php

/**
 * Serve files from storage/app/public/ when symlinks don't work (OVH shared hosting).
 * Pro files (pro/*) require authentication + access matrix check.
 */

$path = ltrim($_SERVER['PATH_INFO'] ?? $_GET['path'] ?? '', '/');

if (empty($path)) {
    http_response_code(404);
    exit;
}

// Prevent directory traversal
if (str_contains($path, '..')) {
    http_response_code(403);
    exit;
}

$fullPath = __DIR__ . '/../storage/app/public/' . $path;

if (! is_file($fullPath)) {
    http_response_code(404);
    exit;
}

// Check if file is in a protected directory
if (str_starts_with($path, 'pro/')) {
    // Boot Laravel for auth check
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ]);

    // Decrypt session cookie and find user
    $user = null;
    $cookieName = config('session.cookie', 'laravel_session');
    $encryptedSessionId = $_COOKIE[$cookieName] ?? null;

    if ($encryptedSessionId) {
        try {
            $decrypted = \Illuminate\Support\Facades\Crypt::decryptString(
                $encryptedSessionId
            );
            // Laravel prefixes session ID with HMAC hash + pipe separator
            $sessionId = str_contains($decrypted, '|') ? explode('|', $decrypted, 2)[1] : $decrypted;

            $session = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('id', $sessionId)
                ->first();

            if ($session && $session->user_id) {
                $user = \App\Models\User::with('roles')->find($session->user_id);
            }
        } catch (\Throwable $e) {
            // Decryption failed — not authenticated
        }
    }

    // Admin has full access
    if ($user && $user->hasRole('admin')) {
        // OK — serve file
    } elseif ($user && $user->hasRole('pro')) {
        $proAccount = $user->loadMissing('proAccount')->proAccount;
        if (! $proAccount || $proAccount->status !== 'approved') {
            http_response_code(403);
            exit;
        }

        // Extract content type slug from path: pro/{content-type-slug}/filename
        $pathParts = explode('/', $path);
        $contentTypeSlug = $pathParts[1] ?? null;

        if ($contentTypeSlug) {
            $contentType = \App\Models\ProContentType::where('slug', $contentTypeSlug)->first();
            if ($contentType) {
                $hasAccess = $proAccount->proType->contentTypes()
                    ->where('pro_content_types.id', $contentType->id)
                    ->exists();
                if (! $hasAccess) {
                    http_response_code(403);
                    exit;
                }
            }
        }
    } else {
        http_response_code(403);
        exit;
    }
}

// Determine content type
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'svg' => 'image/svg+xml',
    'webp' => 'image/webp',
    'pdf' => 'application/pdf',
    'mp3' => 'audio/mpeg',
    'mp4' => 'video/mp4',
    'css' => 'text/css',
    'js' => 'application/javascript',
];

$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
$mime = $mimeTypes[$ext] ?? mime_content_type($fullPath) ?: 'application/octet-stream';

$isProtected = str_starts_with($path, 'pro/');

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: ' . ($isProtected ? 'private, no-cache' : 'public, max-age=604800'));

readfile($fullPath);
