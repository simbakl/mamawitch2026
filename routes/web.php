<?php

use App\Http\Controllers\AdminAudioController;
use App\Http\Controllers\Auth\AccountSetupController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProAudioController;
use App\Http\Controllers\ProController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TechSheetPdfController;
use Illuminate\Support\Facades\Route;

// Sitemap & robots.txt
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    // Block all crawlers on non-production environments
    if (app()->environment('production') && ! str_contains(config('app.url'), 'preprod')) {
        $content = "User-agent: *\nAllow: /\n\nDisallow: /admin\nDisallow: /pro\nDisallow: /auth\nDisallow: /livewire\nDisallow: /tech-sheet\nDisallow: /deploy\n\nSitemap: " . url('/sitemap.xml');
    } else {
        $content = "User-agent: *\nDisallow: /";
    }

    return response($content, 200)->header('Content-Type', 'text/plain');
});

// Public pages
Route::get('/', [PageController::class, 'home'])->name('home');

Route::middleware('page.active:concerts')->group(function () {
    Route::get('/concerts', [PageController::class, 'concerts'])->name('concerts');
});

Route::middleware('page.active:actus')->group(function () {
    Route::get('/actus', [PageController::class, 'newsIndex'])->name('news.index');
    Route::get('/actus/categorie/{slug}', [PageController::class, 'newsByCategory'])->name('news.category');
    Route::get('/actus/{slug}', [PageController::class, 'newsShow'])->name('news.show');
});

Route::middleware('page.active:le-groupe')->group(function () {
    Route::get('/le-groupe', [PageController::class, 'band'])->name('band');
});

Route::middleware('page.active:galerie')->group(function () {
    Route::get('/galerie', [PageController::class, 'galleryIndex'])->name('gallery.index');
    Route::get('/galerie/{slug}', [PageController::class, 'galleryShow'])->name('gallery.show');
});

Route::middleware('page.active:videos')->group(function () {
    Route::get('/videos', [PageController::class, 'videos'])->name('videos');
});

Route::middleware('page.active:discographie')->group(function () {
    Route::get('/discographie', [PageController::class, 'discography'])->name('discography');
    Route::get('/discographie/{slug}', [PageController::class, 'releaseShow'])->name('release.show');
});

Route::middleware('page.active:contact')->group(function () {
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
});

// Tech Sheet PDF
Route::get('/tech-sheet/pdf', [TechSheetPdfController::class, 'generate'])
    ->middleware('auth')
    ->name('tech-sheet.pdf');

// Admin audio streaming
Route::get('/admin/audio/{track}', [AdminAudioController::class, 'stream'])
    ->middleware('auth')
    ->name('admin.audio.stream');

// Pro - Public
Route::middleware('page.active:pro')->group(function () {
    Route::get('/pro/demande', [ProController::class, 'accessRequest'])->name('pro.request');
    Route::post('/pro/demande', [ProController::class, 'accessRequestSubmit'])->name('pro.request.submit');
    Route::get('/pro/invitation/{token}', [ProController::class, 'invitation'])->name('pro.invitation');
});

// Pro - Authenticated
Route::middleware(['page.active:pro', 'auth', 'pro'])->prefix('pro')->name('pro.')->group(function () {
    Route::get('/', [ProController::class, 'dashboard'])->name('dashboard');
    Route::get('/content/{slug}', [ProController::class, 'content'])->name('content');
    Route::get('/download/{type}/{filename}', [ProController::class, 'downloadFile'])->where('filename', '.*')->name('download');
    Route::get('/download-zip/{type}', [ProController::class, 'downloadZip'])->name('download.zip');
    Route::get('/project/{project}', [ProController::class, 'musicProject'])->name('project');
});

// Pro - Secure audio (chunked streaming)
Route::middleware(['page.active:pro', 'auth', 'pro'])->group(function () {
    Route::get('/pro/audio/{track}/info', [ProAudioController::class, 'info'])->name('pro.audio.info');
    Route::get('/pro/audio/{track}/chunk/{index}', [ProAudioController::class, 'chunk'])->name('pro.audio.chunk');
});
Route::get('/pro/audio/{track}', [ProAudioController::class, 'stream'])
    ->middleware(['page.active:pro', 'auth', 'pro'])
    ->name('pro.audio.stream');

// Deploy route is registered in routes/deploy.php (no middleware)

// Account setup & password reset
Route::get('/account/setup/{token}', [AccountSetupController::class, 'show'])->name('account.setup');
Route::post('/account/setup/{token}', [AccountSetupController::class, 'store'])->name('account.setup.store');
Route::get('/forgot-password', [AccountSetupController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [AccountSetupController::class, 'sendResetLink'])->name('password.email');

// Auth
Route::get('/login', fn () => redirect('/admin/login'))->name('login');
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Static pages (must be last to avoid catching other routes)
Route::get('/{slug}', [PageController::class, 'staticPage'])->name('static.page');
