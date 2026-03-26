<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TechSheetPdfController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/concerts', [PageController::class, 'concerts'])->name('concerts');
Route::get('/actus', [PageController::class, 'newsIndex'])->name('news.index');
Route::get('/actus/categorie/{slug}', [PageController::class, 'newsByCategory'])->name('news.category');
Route::get('/actus/{slug}', [PageController::class, 'newsShow'])->name('news.show');
Route::get('/le-groupe', [PageController::class, 'band'])->name('band');
Route::get('/galerie', [PageController::class, 'galleryIndex'])->name('gallery.index');
Route::get('/galerie/{slug}', [PageController::class, 'galleryShow'])->name('gallery.show');
Route::get('/videos', [PageController::class, 'videos'])->name('videos');
Route::get('/discographie', [PageController::class, 'discography'])->name('discography');
Route::get('/discographie/{slug}', [PageController::class, 'releaseShow'])->name('release.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');

// Tech Sheet PDF
Route::get('/tech-sheet/pdf', [TechSheetPdfController::class, 'generate'])
    ->middleware('auth')
    ->name('tech-sheet.pdf');

// Google SSO
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Static pages (must be last to avoid catching other routes)
Route::get('/{slug}', [PageController::class, 'staticPage'])->name('static.page');
