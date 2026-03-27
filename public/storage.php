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
    // Boot Laravel for auth + access matrix check
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle(Illuminate\Http\Request::capture());

    $user = auth()->user();

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
        // e.g. pro/logos-vectoriels/logo.png → logos-vectoriels
        //      pro/photos-hd/photo.jpg → photos-hd
        $pathParts = explode('/', $path);
        $contentTypeSlug = $pathParts[1] ?? null;

        if ($contentTypeSlug) {
            $contentType = App\Models\ProContentType::where('slug', $contentTypeSlug)->first();
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
