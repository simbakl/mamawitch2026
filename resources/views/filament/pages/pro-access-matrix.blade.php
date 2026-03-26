<x-filament-panels::page>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr>
                    <th class="text-left p-3 bg-gray-800 rounded-tl-lg font-heading uppercase tracking-wider text-gray-300">
                        Contenu
                    </th>
                    @foreach ($this->getProTypes() as $proType)
                        <th class="text-center p-3 bg-gray-800 font-heading uppercase tracking-wider text-gray-300 {{ $loop->last ? 'rounded-tr-lg' : '' }}">
                            {{ $proType->name }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($this->getContentTypes() as $contentType)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                        <td class="p-3 font-medium text-gray-200">
                            {{ $contentType->name }}
                        </td>
                        @foreach ($this->getProTypes() as $proType)
                            <td class="text-center p-3">
                                <button
                                    wire:click="toggle({{ $proType->id }}, {{ $contentType->id }})"
                                    class="w-8 h-8 rounded-md border-2 transition-all duration-200 flex items-center justify-center mx-auto cursor-pointer
                                        {{ in_array($contentType->id, $this->matrix[$proType->id] ?? [])
                                            ? 'bg-green-600 border-green-500 text-white'
                                            : 'bg-transparent border-gray-600 text-transparent hover:border-gray-400' }}"
                                >
                                    @if (in_array($contentType->id, $this->matrix[$proType->id] ?? []))
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </button>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-xs text-gray-500">
        Cliquez sur une case pour activer/désactiver l'accès. Les modifications sont enregistrées automatiquement.
    </div>
</x-filament-panels::page>
