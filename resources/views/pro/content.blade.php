@extends('layouts.pro')

@section('title', $contentType->name)

@section('content')
<section class="py-20 px-4">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('pro.dashboard') }}" class="text-gray-500 hover:text-white text-sm font-heading uppercase tracking-wider transition-colors">
                &larr; Dashboard
            </a>
        </div>

        <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mb-8">{{ $contentType->name }}</h1>

        {{-- Rich text content (rider, bio, conditions) --}}
        @if ($contentPage?->body)
            <div class="prose prose-invert max-w-none bg-mw-dark rounded-lg p-8 border border-white/5">
                {!! $contentPage->body !!}
            </div>
        @endif

        {{-- Contact booking (structured data - multiple contacts) --}}
        @if ($contentType->slug === 'contact-booking-direct' && $contentPage?->data)
            <div class="space-y-4">
                @foreach ($contentPage->data as $contact)
                    <div class="bg-mw-dark rounded-lg p-6 border border-white/5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if (! empty($contact['name']))
                                <div>
                                    <span class="text-gray-500 text-xs font-heading uppercase tracking-wider">Nom</span>
                                    <p class="text-white mt-1">{{ $contact['name'] }}</p>
                                </div>
                            @endif
                            @if (! empty($contact['role']))
                                <div>
                                    <span class="text-gray-500 text-xs font-heading uppercase tracking-wider">Rôle</span>
                                    <p class="text-white mt-1">{{ $contact['role'] }}</p>
                                </div>
                            @endif
                            @if (! empty($contact['phone']))
                                <div>
                                    <span class="text-gray-500 text-xs font-heading uppercase tracking-wider">Téléphone</span>
                                    <p class="text-white mt-1"><a href="tel:{{ $contact['phone'] }}" class="hover:text-mw-red transition-colors">{{ $contact['phone'] }}</a></p>
                                </div>
                            @endif
                            @if (! empty($contact['email']))
                                <div>
                                    <span class="text-gray-500 text-xs font-heading uppercase tracking-wider">Email</span>
                                    <p class="text-white mt-1"><a href="mailto:{{ $contact['email'] }}" class="hover:text-mw-red transition-colors">{{ $contact['email'] }}</a></p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Revue de presse --}}
        @if ($contentType->slug === 'revue-de-presse' && $contentPage?->data)
            <div class="space-y-3">
                @foreach ($contentPage->data as $article)
                    <div class="bg-mw-dark rounded-lg p-5 border border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-heading uppercase text-sm">{{ $article['title'] ?? '' }}</h3>
                            <p class="text-gray-500 text-xs mt-1">
                                @if (! empty($article['media'])) {{ $article['media'] }} @endif
                                @if (! empty($article['date'])) — {{ \Carbon\Carbon::parse($article['date'])->format('d/m/Y') }} @endif
                            </p>
                        </div>
                        @if (! empty($article['url']))
                            <a href="{{ $article['url'] }}" target="_blank" class="text-mw-red text-xs font-heading uppercase tracking-wider hover:text-white transition-colors flex-shrink-0">
                                Lire &rarr;
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Photos HD --}}
        @if ($contentType->slug === 'photos-hd' && $contentPage?->files)
            <div class="flex justify-end mb-4">
                <a href="{{ route('pro.download.zip', ['type' => 'photos-hd']) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-xs rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Tout télécharger (ZIP)
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($contentPage->files as $file)
                    <div class="group relative aspect-square rounded-lg overflow-hidden bg-mw-dark border border-white/5">
                        <img src="{{ asset('storage/pro/' . $file) }}" alt="" class="w-full h-full object-cover">
                        <a href="{{ route('pro.download', ['type' => 'photos-hd', 'filename' => $file]) }}"
                           class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-white text-xs font-heading uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Télécharger HD
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Logos --}}
        @if ($contentType->slug === 'logos-vectoriels' && $contentPage?->files)
            <div class="flex justify-end mb-4">
                <a href="{{ route('pro.download.zip', ['type' => 'logos-vectoriels']) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-xs rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Tout télécharger (ZIP)
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($contentPage->files as $file)
                    <a href="{{ route('pro.download', ['type' => 'logos-vectoriels', 'filename' => $file]) }}"
                       class="bg-mw-dark rounded-lg p-6 border border-white/5 hover:border-mw-red/30 transition-colors flex flex-col items-center gap-3 group">
                        @if (str_ends_with(strtolower($file), '.svg') || str_ends_with(strtolower($file), '.png') || str_ends_with(strtolower($file), '.jpg'))
                            <img src="{{ asset('storage/pro/' . $file) }}" alt="" class="h-16 object-contain">
                        @else
                            <svg class="w-12 h-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        @endif
                        <span class="text-xs text-gray-400 group-hover:text-white transition-colors truncate w-full text-center">{{ basename($file) }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Fiche technique --}}
        @if ($contentType->slug === 'fiche-technique')
            <div class="bg-mw-dark rounded-lg p-8 border border-white/5 text-center">
                <svg class="w-16 h-16 text-mw-red mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <h3 class="font-heading uppercase tracking-wider mb-2">Fiche Technique PDF</h3>
                <p class="text-gray-400 text-sm mb-6">Document auto-généré avec l'équipement et les besoins techniques du groupe.</p>
                <a href="{{ route('tech-sheet.pdf') }}" target="_blank"
                   class="inline-block px-8 py-3 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-sm transition-all rounded">
                    Télécharger le PDF
                </a>
            </div>
        @endif

        {{-- Plan de scène --}}
        @if ($contentType->slug === 'plan-de-scene')
            <div class="bg-mw-dark rounded-lg p-8 border border-white/5 text-center">
                <p class="text-gray-400">Le plan de scène sera disponible prochainement.</p>
            </div>
        @endif

        {{-- Empty state --}}
        @if (! $contentPage?->body && ! $contentPage?->data && ! $contentPage?->files && ! in_array($contentType->slug, ['fiche-technique', 'plan-de-scene']))
            <div class="bg-mw-dark rounded-lg p-8 border border-white/5 text-center">
                <p class="text-gray-400">Ce contenu n'est pas encore disponible.</p>
            </div>
        @endif
    </div>
</section>
@endsection
