<?php

/**
 * Serve files from storage/app/public/ when symlinks don't work (OVH shared hosting).
 * Pro files (pro/*) require authentication.
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

// Protected directories — require authenticated session
$protectedPrefixes = ['pro/'];
$isProtected = false;

foreach ($protectedPrefixes as $prefix) {
    if (str_starts_with($path, $prefix)) {
        $isProtected = true;
        break;
    }
}

if ($isProtected) {
    // Boot Laravel to check authentication
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
    $kernel->handle($request);

    $user = auth()->user();
    if (! $user || (! $user->hasRole('pro') && ! $user->hasRole('admin'))) {
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

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: ' . ($isProtected ? 'private, no-cache' : 'public, max-age=604800'));

readfile($fullPath);
