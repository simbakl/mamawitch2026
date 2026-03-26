@extends('layouts.app')
@section('title', $release->title)
@section('meta_description', $release->title . ' - ' . ucfirst($release->type) . ' de Mama Witch.')
@section('og_type', 'music.album')
@if ($release->cover)
    @section('og_image', asset('storage/' . $release->cover))
@endif

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('discography') }}" class="text-gray-400 hover:text-white font-heading uppercase text-sm tracking-wider transition-colors">
            &larr; Retour à la discographie
        </a>

        <div class="flex flex-col md:flex-row gap-8 mt-6">
            @if ($release->cover)
                <img src="{{ asset('storage/' . $release->cover) }}" alt="{{ $release->title }}" loading="lazy" class="w-64 h-64 object-cover rounded-lg shadow-2xl flex-shrink-0">
            @endif
            <div>
                <span class="px-2 py-0.5 text-xs font-heading uppercase rounded {{ match($release->type) { 'album' => 'bg-red-500/20 text-red-400', 'ep' => 'bg-amber-500/20 text-amber-400', default => 'bg-blue-500/20 text-blue-400' } }}">
                    {{ strtoupper($release->type) }}
                </span>
                <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mt-2">{{ $release->title }}</h1>
                @if ($release->release_date)
                    <p class="text-gray-400 mt-1">{{ $release->release_date->translatedFormat('d F Y') }}</p>
                @endif

                {{-- Platform links --}}
                <div class="flex flex-wrap gap-2 mt-4">
                    @if ($release->spotify_url)
                        <a href="{{ $release->spotify_url }}" target="_blank" class="px-4 py-2 bg-[#1DB954]/20 text-[#1DB954] hover:bg-[#1DB954] hover:text-black text-xs font-heading uppercase rounded transition-all">Spotify</a>
                    @endif
                    @if ($release->bandcamp_url)
                        <a href="{{ $release->bandcamp_url }}" target="_blank" class="px-4 py-2 bg-[#629aa9]/20 text-[#629aa9] hover:bg-[#629aa9] hover:text-black text-xs font-heading uppercase rounded transition-all">Bandcamp</a>
                    @endif
                    @if ($release->apple_music_url)
                        <a href="{{ $release->apple_music_url }}" target="_blank" class="px-4 py-2 bg-[#fc3c44]/20 text-[#fc3c44] hover:bg-[#fc3c44] hover:text-white text-xs font-heading uppercase rounded transition-all">Apple Music</a>
                    @endif
                    @if ($release->deezer_url)
                        <a href="{{ $release->deezer_url }}" target="_blank" class="px-4 py-2 bg-[#a238ff]/20 text-[#a238ff] hover:bg-[#a238ff] hover:text-white text-xs font-heading uppercase rounded transition-all">Deezer</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Player embed --}}
        @if ($release->player_embed_url)
            <div class="mt-8">
                <iframe src="{{ $release->player_embed_url }}" title="{{ $release->title }}" loading="lazy" class="w-full h-[380px] rounded-lg" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        @endif

        {{-- Description --}}
        @if ($release->description)
            <div class="mt-8">
                <h2 class="font-heading uppercase text-lg tracking-wider text-gray-400 mb-3">À propos</h2>
                <p class="text-gray-300 leading-relaxed">{{ $release->description }}</p>
            </div>
        @endif

        {{-- Tracklist --}}
        @if ($release->tracks->count())
            <div class="mt-8">
                <h2 class="font-heading uppercase text-lg tracking-wider text-gray-400 mb-3">Tracklist</h2>
                <ol class="space-y-1">
                    @foreach ($release->tracks as $track)
                        <li class="flex justify-between text-gray-300 py-2 border-b border-white/5">
                            <span><span class="text-mw-red mr-3 font-display">{{ str_pad($track->track_number, 2, '0', STR_PAD_LEFT) }}</span> {{ $track->title }}</span>
                            @if ($track->duration)
                                <span class="text-gray-500">{{ $track->duration }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif

        {{-- Credits --}}
        @if ($release->credits)
            <div class="mt-8">
                <h2 class="font-heading uppercase text-lg tracking-wider text-gray-400 mb-3">Crédits</h2>
                <p class="text-gray-400 text-sm whitespace-pre-line">{{ $release->credits }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
