<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'killian.lesaint@gmail.com'],
            ['name' => 'Killian']
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
        $output[] = 'Admin user ready: ' . $admin->email;

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
