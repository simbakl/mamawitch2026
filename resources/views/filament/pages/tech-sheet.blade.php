<x-filament-panels::page>
    {{-- Global requirements form --}}
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Enregistrer les besoins globaux
            </x-filament::button>
        </div>
    </form>

    {{-- Overview of all members equipment --}}
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4 text-gray-200">Récapitulatif du matériel par membre</h2>

        @foreach ($this->getMembers() as $member)
            <div class="bg-white/5 rounded-xl p-4 mb-4">
                <h3 class="text-lg font-semibold text-white mb-1">{{ $member->name }} <span class="text-sm text-gray-400 font-normal">— {{ $member->instruments }}</span></h3>

                @if ($member->equipment->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                        @foreach ($member->equipment as $item)
                            <div class="flex items-center gap-2 text-sm text-gray-300">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($item->category) {
                                        'instrument' => 'bg-blue-500/20 text-blue-400',
                                        'amp' => 'bg-orange-500/20 text-orange-400',
                                        'effect' => 'bg-purple-500/20 text-purple-400',
                                        'accessory' => 'bg-gray-500/20 text-gray-400',
                                        default => 'bg-gray-500/20 text-gray-400',
                                    } }}">
                                    {{ $item->category_label }}
                                </span>
                                <span>{{ $item->name }}</span>
                                @if ($item->notes)
                                    <span class="text-gray-500 text-xs">— {{ $item->notes }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 mt-1">Aucun matériel renseigné</p>
                @endif

                @if ($member->techRequirement)
                    <div class="mt-3 pt-3 border-t border-white/10">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Besoins techniques</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 text-sm text-gray-300">
                            @if ($member->techRequirement->monitors)
                                <div><span class="text-gray-500">Retours :</span> {{ $member->techRequirement->monitors }}</div>
                            @endif
                            @if ($member->techRequirement->microphones)
                                <div><span class="text-gray-500">Micros/DI :</span> {{ $member->techRequirement->microphones }}</div>
                            @endif
                            @if ($member->techRequirement->power)
                                <div><span class="text-gray-500">Électricité :</span> {{ $member->techRequirement->power }}</div>
                            @endif
                            @if ($member->techRequirement->monitoring)
                                <div><span class="text-gray-500">Monitoring :</span> {{ $member->techRequirement->monitoring }}</div>
                            @endif
                            @if ($member->techRequirement->other)
                                <div><span class="text-gray-500">Divers :</span> {{ $member->techRequirement->other }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Generate PDF button --}}
    <div class="mt-6">
        <a href="{{ route('tech-sheet.pdf') }}" target="_blank">
            <x-filament::button color="success" icon="heroicon-o-document-arrow-down">
                Générer la Fiche Technique PDF
            </x-filament::button>
        </a>
    </div>
</x-filament-panels::page>
