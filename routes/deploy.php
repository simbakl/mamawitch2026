<?php

use App\Http\Controllers\DeployController;
use Illuminate\Support\Facades\Route;

// No middleware — this route runs before DB tables exist
Route::get('/deploy/run', [DeployController::class, 'run'])->name('deploy.run');
