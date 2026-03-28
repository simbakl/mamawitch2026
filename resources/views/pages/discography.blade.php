@extends('layouts.app')
@section('title', 'Discographie')
@section('meta_description', 'Discographie complète de Mama Witch - EPs, singles et albums.')

@php
    $typeLabels = ['album' => 'Albums', 'ep' => 'EPs', 'single' => 'Singles'];
    $types = $releases->pluck('type')->unique()->filter();
@endphp

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-5xl mx-auto" x-data="{ filter: 'all' }">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-12">Discographie</h1>

        {{-- Type filters --}}
        @if ($types->count() > 1)
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <button @click="filter = 'all'"
                    :class="filter === 'all' ? 'bg-mw-red text-white' : 'bg-mw-dark text-gray-400 hover:text-white border border-white/10'"
                    class="px-4 py-1.5 text-xs font-heading uppercase tracking-wider rounded transition-all cursor-pointer">
                    Tout
                </button>
                @foreach ($types as $type)
                    <button @click="filter = '{{ $type }}'"
                        :class="filter === '{{ $type }}' ? 'bg-mw-red text-white' : 'bg-mw-dark text-gray-400 hover:text-white border border-white/10'"
                        class="px-4 py-1.5 text-xs font-heading uppercase tracking-wider rounded transition-all cursor-pointer">
                        {{ $typeLabels[$type] ?? strtoupper($type) }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="space-y-12">
            @forelse ($releases as $release)
                <div x-show="filter === 'all' || filter === '{{ $release->type }}'" x-transition class="flex flex-col md:flex-row gap-8 bg-mw-dark rounded-lg p-6 border border-white/5">
                    {{-- Cover --}}
                    @if ($release->cover)
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . $release->cover) }}" alt="{{ $release->title }}" loading="lazy" class="w-48 h-48 object-cover rounded-lg shadow-xl">
                        </div>
                    @endif

                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2 py-0.5 text-xs font-heading uppercase rounded
                                {{ match($release->type) { 'album' => 'bg-red-500/20 text-red-400', 'ep' => 'bg-amber-500/20 text-amber-400', 'single' => 'bg-blue-500/20 text-blue-400', default => 'bg-gray-500/20 text-gray-400' } }}">
                                {{ strtoupper($release->type) }}
                            </span>
                            @if ($release->release_date)
                                <span class="text-gray-500 text-sm">{{ $release->release_date->format('Y') }}</span>
                            @endif
                        </div>

                        <h2 class="font-display text-2xl uppercase mb-4">
                            <a href="{{ route('release.show', $release->slug) }}" class="hover:text-mw-red transition-colors">{{ $release->title }}</a>
                        </h2>

                        {{-- Tracklist --}}
                        @if ($release->tracks->count())
                            <ol class="space-y-1 mb-4">
                                @foreach ($release->tracks as $track)
                                    <li class="flex justify-between text-sm text-gray-400 py-1 border-b border-white/5">
                                        <span><span class="text-gray-600 mr-2">{{ $track->track_number }}.</span> {{ $track->title }}</span>
                                        @if ($track->duration)
                                            <span class="text-gray-600">{{ $track->duration }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @endif

                        {{-- Platform links --}}
                        <div class="flex flex-wrap gap-2">
                            @if ($release->spotify_url)
                                <a href="{{ $release->spotify_url }}" target="_blank" class="px-3 py-1 bg-[#1DB954]/20 text-[#1DB954] hover:bg-[#1DB954] hover:text-black text-xs font-heading uppercase rounded transition-all">Spotify</a>
                            @endif
                            @if ($release->bandcamp_url)
                                <a href="{{ $release->bandcamp_url }}" target="_blank" class="px-3 py-1 bg-[#629aa9]/20 text-[#629aa9] hover:bg-[#629aa9] hover:text-black text-xs font-heading uppercase rounded transition-all">Bandcamp</a>
                            @endif
                            @if ($release->apple_music_url)
                                <a href="{{ $release->apple_music_url }}" target="_blank" class="px-3 py-1 bg-[#fc3c44]/20 text-[#fc3c44] hover:bg-[#fc3c44] hover:text-white text-xs font-heading uppercase rounded transition-all">Apple Music</a>
                            @endif
                            @if ($release->deezer_url)
                                <a href="{{ $release->deezer_url }}" target="_blank" class="px-3 py-1 bg-[#a238ff]/20 text-[#a238ff] hover:bg-[#a238ff] hover:text-white text-xs font-heading uppercase rounded transition-all">Deezer</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-12">Aucune release pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
