<?php

use App\Http\Controllers\DeployController;
use Illuminate\Support\Facades\Route;

// No middleware — this route runs before DB tables exist
Route::get('/deploy/run', [DeployController::class, 'run'])->name('deploy.run');

// Serve storage files when symlink doesn't work (OVH shared hosting)
Route::get('/storage-serve/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);

    if (! file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*')->name('storage.serve');
