<x-filament-panels::page>
    {{-- Current version --}}
    <x-filament::section>
        <x-slot name="heading">Version actuelle</x-slot>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Commit</p>
                <p class="font-mono text-sm">{{ $currentCommit }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                <p class="text-sm">{{ $currentDate }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Message</p>
                <p class="text-sm">{{ $currentMessage }}</p>
            </div>
        </div>
    </x-filament::section>

    {{-- Actions --}}
    <x-filament::section>
        <x-slot name="heading">Mises à jour</x-slot>
        <div class="flex flex-wrap gap-3">
            <x-filament::button wire:click="checkForUpdates" icon="heroicon-o-magnifying-glass" color="info">
                Vérifier les mises à jour
            </x-filament::button>

            @if ($updateAvailable)
                <x-filament::button wire:click="runUpdate" icon="heroicon-o-arrow-down-tray" color="success">
                    Mettre à jour le site
                </x-filament::button>
            @endif

            <x-filament::button wire:click="clearCache" icon="heroicon-o-trash" color="warning">
                Vider le cache
            </x-filament::button>
        </div>

        @if ($checked && $updateAvailable)
            <div class="mt-4 p-4 rounded-lg bg-warning-50 dark:bg-warning-400/10 border border-warning-300 dark:border-warning-400/20">
                <p class="font-medium text-warning-600 dark:text-warning-400">Mise à jour disponible</p>
                <p class="mt-1 text-sm text-warning-600 dark:text-warning-400">
                    Dernière version : <span class="font-mono">{{ $remoteCommit }}</span> — {{ $remoteMessage }}
                </p>
                @if ($hasPendingMigrations)
                    <p class="mt-1 text-sm text-warning-600 dark:text-warning-400">
                        Des migrations de base de données seront exécutées.
                    </p>
                @endif
            </div>
        @endif
    </x-filament::section>

    {{-- Log --}}
    @if ($logHtml)
        <x-filament::section>
            <x-slot name="heading">Journal</x-slot>
            <div class="space-y-1">
                {!! $logHtml !!}
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
