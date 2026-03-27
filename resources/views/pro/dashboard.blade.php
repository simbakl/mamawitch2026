@extends('layouts.pro')

@section('title', 'Dashboard Pro')

@section('content')
<section class="py-20 px-4">
    <div class="max-w-6xl mx-auto">
        {{-- Welcome --}}
        <div class="mb-12">
            <span class="text-mw-red text-xs font-heading uppercase tracking-wider">{{ $proAccount->proType->name }}</span>
            <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mt-2">
                Bienvenue, {{ $proAccount->first_name }}
            </h1>
            <p class="text-gray-400 mt-2">{{ $proAccount->structure }}</p>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($accessibleContentTypes as $contentType)
                @php
                    $page = $contentPages[$contentType->slug] ?? null;
                    $alwaysAvailable = in_array($contentType->slug, ['fiche-technique', 'plan-de-scene']);
                    $hasContent = $alwaysAvailable || ($page && ($page->body || $page->data || $page->files));

                    $icons = [
                        'fiche-technique' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>',
                        'plan-de-scene' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>',
                        'hospitality-rider' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>',
                        'photos-hd' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z"/>',
                        'logos-vectoriels' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072"/>',
                        'bio-longue-presse' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5"/>',
                        'revue-de-presse' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5"/>',
                        'conditions-booking' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.25 7.756a4.5 4.5 0 100 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'contact-booking-direct' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>',
                    ];
                    $icon = $icons[$contentType->slug] ?? $icons['fiche-technique'];
                @endphp
                <a href="{{ route('pro.content', $contentType->slug) }}"
                   class="bg-mw-dark rounded-lg p-6 border border-white/5 hover:border-mw-red/30 transition-all group {{ ! $hasContent ? 'opacity-50' : '' }}">
                    <div class="flex items-start gap-4">
                        <svg class="w-8 h-8 text-mw-red flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $icon !!}</svg>
                        <div>
                            <h3 class="font-heading uppercase text-sm tracking-wider group-hover:text-mw-red transition-colors">{{ $contentType->name }}</h3>
                            @if ($contentType->description)
                                <p class="text-gray-500 text-xs mt-1">{{ $contentType->description }}</p>
                            @endif
                            @if (! $hasContent)
                                <p class="text-gray-600 text-xs mt-2 italic">Bientôt disponible</p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Music Projects --}}
        @if ($musicProjects->count())
        <div class="mt-20">
            <h2 class="font-display text-2xl uppercase tracking-wider mb-8">
                <span class="text-mw-red">///</span> Ecoute privee
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($musicProjects as $project)
                    <a href="{{ route('pro.project', $project) }}"
                       class="bg-mw-dark rounded-lg p-6 border border-white/5 hover:border-mw-amber/30 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-mw-amber/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-mw-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-heading uppercase tracking-wider group-hover:text-mw-amber transition-colors">{{ $project->title }}</h3>
                                <p class="text-gray-500 text-xs mt-1">{{ $project->tracks->count() }} piste(s)</p>
                                @if ($project->description)
                                    <p class="text-gray-400 text-xs mt-1">{{ Str::limit($project->description, 80) }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
