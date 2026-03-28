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

        // === PHASE 1: Critical — must succeed or stop ===
        try {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            $output[] = 'File caches cleared: OK';
        } catch (\Throwable $e) {
            $output[] = 'File caches: FAIL — ' . $e->getMessage();
            return $this->respond('error', $output, 'Cannot clear caches');
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output[] = 'Migrations: OK — ' . trim(Artisan::output());
        } catch (\Throwable $e) {
            $output[] = 'Migrations: FAIL — ' . $e->getMessage();
            return $this->respond('error', $output, 'Migration failed');
        }

        try {
            Artisan::call('cache:clear');
            $output[] = 'DB cache cleared: OK';
        } catch (\Throwable $e) {
            $output[] = 'DB cache: FAIL — ' . $e->getMessage();
            return $this->respond('error', $output, 'Cannot clear DB cache');
        }

        try {
            Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
            $output[] = 'Roles seeded: OK';
        } catch (\Throwable $e) {
            $output[] = 'Roles: FAIL — ' . $e->getMessage();
            return $this->respond('error', $output, 'Role seeding failed');
        }

        try {
            $admin = User::firstOrCreate(
                ['email' => 'killian.lesaint@gmail.com'],
                ['name' => 'Killian']
            );
            if (! $admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }
            $output[] = 'Admin user: OK — ' . $admin->email;
        } catch (\Throwable $e) {
            $output[] = 'Admin user: FAIL — ' . $e->getMessage();
            return $this->respond('error', $output, 'Admin user creation failed');
        }

        // === PHASE 2: Secondary — nice to have, don't block ===
        try {
            Artisan::call('db:seed', ['--class' => 'ProContentSeeder', '--force' => true]);
            $output[] = 'Pro content seeded: OK';
        } catch (\Throwable $e) {
            $output[] = 'Pro content: SKIP — ' . $e->getMessage();
        }

        try {
            Artisan::call('optimize');
            $output[] = 'Optimized: OK';
        } catch (\Throwable $e) {
            $output[] = 'Optimize: SKIP — ' . $e->getMessage();
        }

        try {
            if (! file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
                $output[] = 'Storage linked: OK';
            } else {
                $output[] = 'Storage link: already exists';
            }
        } catch (\Throwable $e) {
            $output[] = 'Storage link: SKIP — ' . $e->getMessage();
        }

        try {
            Artisan::call('filament:assets');
            $output[] = 'Filament assets: OK';
        } catch (\Throwable $e) {
            $output[] = 'Filament assets: SKIP — ' . $e->getMessage();
        }

        return $this->respond('ok', $output);
    }

    protected function respond(string $status, array $steps, ?string $error = null)
    {
        $data = ['status' => $status, 'steps' => $steps];
        if ($error) {
            $data['error'] = $error;
        }

        return response()->json($data, $status === 'ok' ? 200 : 500);
    }
}
