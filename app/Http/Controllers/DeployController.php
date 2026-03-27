<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DeployController extends Controller
{
    /**
     * Run deployment tasks (migrations, cache clear, optimize).
     * Secured by a deploy token defined in .env.
     */
    public function run(Request $request)
    {
        $token = config('app.deploy_token');

        if (! $token || $request->query('token') !== $token) {
            abort(404);
        }

        $output = [];

        // Clear caches
        Artisan::call('optimize:clear');
        $output[] = 'Caches cleared';

        // Run migrations
        Artisan::call('migrate', ['--force' => true]);
        $output[] = 'Migrations: ' . trim(Artisan::output());

        // Run seeders (roles, etc.)
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        $output[] = 'Roles seeded';

        // Optimize
        Artisan::call('optimize');
        $output[] = 'Optimized';

        // Storage link
        if (! file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
            $output[] = 'Storage linked';
        }

        // Filament assets
        Artisan::call('filament:assets');
        $output[] = 'Filament assets published';

        return response()->json([
            'status' => 'ok',
            'steps' => $output,
        ]);
    }
}
