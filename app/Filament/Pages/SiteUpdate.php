<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class SiteUpdate extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Mise à jour';

    protected static ?string $title = 'Mise à jour du site';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.site-update';

    public string $currentCommit = '-';

    public string $currentDate = '-';

    public string $currentMessage = '-';

    public string $remoteCommit = '-';

    public string $remoteMessage = '-';

    public bool $updateAvailable = false;

    public bool $checked = false;

    public string $logHtml = '';

    public bool $hasPendingMigrations = false;

    public function mount(): void
    {
        $this->loadCurrentVersion();
    }

    protected function git(string $command): string
    {
        $result = Process::path(base_path())->run('git ' . $command);

        return trim($result->output());
    }

    protected function loadCurrentVersion(): void
    {
        try {
            $this->currentCommit = $this->git('rev-parse --short HEAD') ?: '-';
            $this->currentDate = $this->git('log -1 --format=%ci') ?: '-';
            $this->currentMessage = $this->git('log -1 --format=%s') ?: '-';
        } catch (\Throwable) {
            $this->currentCommit = '-';
            $this->currentDate = '-';
            $this->currentMessage = 'Git non disponible';
        }
    }

    protected function addLog(string $type, string $message): void
    {
        $icon = match ($type) {
            'success' => '&#9989;',
            'error' => '&#10060;',
            default => '&#8505;&#65039;',
        };

        $this->logHtml .= '<div class="flex items-start gap-2 text-sm py-1">'
            . '<span>' . $icon . '</span>'
            . '<span class="font-mono text-gray-300">' . e($message) . '</span>'
            . '</div>';
    }

    public function checkForUpdates(): void
    {
        $this->logHtml = '';
        $this->checked = true;

        $branch = $this->git('rev-parse --abbrev-ref HEAD');
        $this->git('fetch origin ' . $branch);

        $this->remoteCommit = $this->git('rev-parse --short origin/' . $branch) ?: '-';
        $this->remoteMessage = $this->git('log origin/' . $branch . ' -1 --format=%s') ?: '-';

        $behind = (int) $this->git('rev-list HEAD..origin/' . $branch . ' --count');

        if ($behind > 0) {
            $this->updateAvailable = true;
            $this->addLog('info', $behind . ' commit(s) en attente');
        } else {
            $this->updateAvailable = false;
            $this->addLog('success', 'Le site est à jour');
        }

        $this->checkPendingMigrations();
    }

    protected function checkPendingMigrations(): void
    {
        try {
            Artisan::call('migrate:status');
            $this->hasPendingMigrations = str_contains(Artisan::output(), 'Pending');
        } catch (\Throwable) {
            $this->hasPendingMigrations = false;
        }
    }

    public function runUpdate(): void
    {
        $this->logHtml = '';

        $branch = $this->git('rev-parse --abbrev-ref HEAD');
        $pullOutput = $this->git('pull origin ' . $branch);
        $this->addLog('info', 'Git pull : ' . $pullOutput);

        try {
            Artisan::call('migrate:status');
            $hasPending = str_contains(Artisan::output(), 'Pending');

            if ($hasPending) {
                Artisan::call('migrate', ['--force' => true]);
                $this->addLog('success', 'Migrations : OK');
            } else {
                $this->addLog('info', 'Migrations : aucune en attente');
            }
        } catch (\Throwable $e) {
            $this->addLog('error', 'Migrations : ' . $e->getMessage());
        }

        $cacheCommands = [
            'config:cache' => 'Cache config',
            'route:cache' => 'Cache routes',
            'view:cache' => 'Cache vues',
        ];

        foreach ($cacheCommands as $command => $label) {
            try {
                Artisan::call($command);
                $this->addLog('success', $label . ' : OK');
            } catch (\Throwable $e) {
                $this->addLog('error', $label . ' : ' . $e->getMessage());
            }
        }

        if (! file_exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
                $this->addLog('success', 'Storage link : créé');
            } catch (\Throwable $e) {
                $this->addLog('error', 'Storage link : ' . $e->getMessage());
            }
        }

        $this->addLog('success', 'Mise à jour terminée');

        $this->loadCurrentVersion();
        $this->updateAvailable = false;
        $this->checked = false;
    }

    public function clearCache(): void
    {
        $this->logHtml = '';

        $commands = [
            'config:clear' => 'Cache config',
            'route:clear' => 'Cache routes',
            'view:clear' => 'Cache vues',
            'cache:clear' => 'Cache application',
        ];

        foreach ($commands as $command => $label) {
            try {
                Artisan::call($command);
                $this->addLog('success', $label . ' : vidé');
            } catch (\Throwable $e) {
                $this->addLog('error', $label . ' : ' . $e->getMessage());
            }
        }

        $this->addLog('success', 'Tous les caches ont été vidés');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
