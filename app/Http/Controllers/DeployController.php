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

        $steps = [
            'Clear file caches' => fn () => $this->clearFileCaches(),
            'Run migrations' => fn () => $this->migrate(),
            'Clear DB cache' => fn () => Artisan::call('cache:clear'),
            'Seed roles' => fn () => Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]),
            'Seed pro content' => fn () => Artisan::call('db:seed', ['--class' => 'ProContentSeeder', '--force' => true]),
            'Create admin user' => fn () => $this->createAdmin(),
            'Optimize' => fn () => Artisan::call('optimize'),
            'Storage link' => fn () => $this->storageLink(),
            'Filament assets' => fn () => Artisan::call('filament:assets'),
        ];

        foreach ($steps as $name => $step) {
            try {
                $result = $step();
                $output[] = "{$name}: OK" . ($result ? " ({$result})" : '');
            } catch (\Throwable $e) {
                $output[] = "{$name}: FAIL — {$e->getMessage()}";
            }
        }

        return response()->json([
            'status' => 'ok',
            'steps' => $output,
        ]);
    }

    protected function clearFileCaches(): void
    {
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
    }

    protected function migrate(): string
    {
        Artisan::call('migrate', ['--force' => true]);

        return trim(Artisan::output());
    }

    protected function createAdmin(): string
    {
        $admin = User::firstOrCreate(
            ['email' => 'killian.lesaint@gmail.com'],
            ['name' => 'Killian']
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        return $admin->email;
    }

    protected function storageLink(): void
    {
        if (! file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }
    }
}
