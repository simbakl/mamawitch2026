<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\TechSheetPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Tech Sheet PDF
Route::get('/tech-sheet/pdf', [TechSheetPdfController::class, 'generate'])
    ->middleware('auth')
    ->name('tech-sheet.pdf');

// Google SSO
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
