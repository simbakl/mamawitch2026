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
    // Boot Laravel minimally for auth check
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Bootstrap the application (config, providers, etc.)
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ]);

    // Start session manually to read auth cookie
    $sessionConfig = config('session');
    $cookieName = $sessionConfig['cookie'] ?? config('app.name') . '_session';

    $sessionId = $_COOKIE[$cookieName] ?? null;
    $user = null;

    if ($sessionId) {
        // Read session from database
        $session = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('id', $sessionId)
            ->first();

        if ($session) {
            $userId = $session->user_id;
            if ($userId) {
                $user = \App\Models\User::with('roles')->find($userId);
            }
        }
    }

    // Admin has full access
    if ($user && $user->hasRole('admin')) {
        // OK — serve file
    } elseif ($user && $user->hasRole('pro')) {
        $proAccount = $user->proAccount;
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
