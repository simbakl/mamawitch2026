@extends('layouts.app')

@section('content')

{{-- HERO --}}
<section class="relative h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        @if ($hero['image'])
            <img src="{{ asset('storage/' . $hero['image']) }}" alt="Mama Witch - Hard Rock Paris" class="w-full h-full object-cover">
        @else
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="Mama Witch - Hard Rock Paris" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-mw-black/60 via-mw-black/40 to-mw-black"></div>
    </div>

    <div class="relative text-center px-4">
        <img src="{{ asset('images/logo-white.png') }}" alt="Mama Witch" class="h-36 md:h-60 mx-auto mb-6">
        @if ($hero['subtitle'])
            <p class="text-lg md:text-2xl font-heading uppercase tracking-widest text-gray-300 mb-8">{{ $hero['subtitle'] }}</p>
        @endif
        @if ($hero['cta_text'] && $hero['cta_url'])
            <a href="{{ $hero['cta_url'] }}" class="inline-block px-8 py-3 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-sm transition-all duration-300 rounded">
                {{ $hero['cta_text'] }}
            </a>
        @endif
    </div>

    {{-- Scroll indicator --}}
    <a href="#next-section" class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce cursor-pointer hover:text-white transition-colors">
        <svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </a>
</section>

<div id="next-section"></div>

{{-- PROCHAINS CONCERTS --}}
@if ($concerts->count())
<section class="py-20 px-4">
    <div class="max-w-5xl mx-auto">
        <h2 class="font-display text-3xl md:text-4xl uppercase tracking-wider text-center mb-12">Prochains Concerts</h2>

        <div class="space-y-4">
            @foreach ($concerts as $concert)
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between bg-mw-dark rounded-lg p-5 border border-white/5 hover:border-mw-red/30 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="text-center min-w-[60px]">
                            <div class="text-2xl font-display text-mw-red">{{ $concert->date->format('d') }}</div>
                            <div class="text-xs font-heading uppercase text-gray-400">{{ $concert->date->translatedFormat('M Y') }}</div>
                        </div>
                        <div>
                            <h3 class="font-heading uppercase text-lg">{{ $concert->title }}</h3>
                            <p class="text-gray-400 text-sm">{{ $concert->venue }} — {{ $concert->city }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-3 md:mt-0">
                        @if ($concert->status === 'soldout')
                            <span class="px-3 py-1 bg-mw-amber/20 text-mw-amber text-xs font-heading uppercase rounded">Complet</span>
                        @elseif ($concert->status === 'cancelled')
                            <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-heading uppercase rounded">Annulé</span>
                        @elseif ($concert->ticket_url)
                            <a href="{{ $concert->ticket_url }}" target="_blank" class="px-4 py-1.5 bg-mw-red hover:bg-mw-red-dark text-white text-xs font-heading uppercase tracking-wider rounded transition-colors">
                                Billets
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('concerts') }}" class="text-gray-400 hover:text-white font-heading uppercase text-sm tracking-wider transition-colors">
                Voir toutes les dates &rarr;
            </a>
        </div>
    </div>
</section>
@endif

{{-- DERNIERES ACTUS --}}
@if ($news->count())
<section class="py-20 px-4 bg-mw-dark">
    <div class="max-w-6xl mx-auto">
        <h2 class="font-display text-3xl md:text-4xl uppercase tracking-wider text-center mb-12">Actualités</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($news as $article)
                <article class="bg-mw-black rounded-lg overflow-hidden border border-white/5 hover:border-mw-red/30 transition-colors group">
                    @if ($article->featured_image)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    @endif
                    <div class="p-5">
                        @if ($article->category)
                            <span class="text-mw-red text-xs font-heading uppercase tracking-wider">{{ $article->category->name }}</span>
                        @endif
                        <h3 class="font-heading uppercase text-lg mt-1 mb-2 group-hover:text-mw-red transition-colors">{{ $article->title }}</h3>
                        <p class="text-gray-400 text-sm line-clamp-3">{{ $article->excerpt }}</p>
                        @if ($article->hasDetailPage())
                            <a href="{{ route('news.show', $article->slug) }}" class="inline-block mt-3 text-mw-red text-sm font-heading uppercase tracking-wider hover:text-white transition-colors">
                                Lire la suite &rarr;
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('news.index') }}" class="text-gray-400 hover:text-white font-heading uppercase text-sm tracking-wider transition-colors">
                Toutes les actus &rarr;
            </a>
        </div>
    </div>
</section>
@endif

{{-- DERNIERE SORTIE --}}
@if ($latestRelease)
<section class="py-20 px-4">
    <div class="max-w-4xl mx-auto">
        <h2 class="font-display text-3xl md:text-4xl uppercase tracking-wider text-center mb-12">Dernière Sortie</h2>

        <div class="flex flex-col md:flex-row items-center gap-8">
            @if ($latestRelease->cover)
                <img src="{{ asset('storage/' . $latestRelease->cover) }}" alt="{{ $latestRelease->title }}" loading="lazy" class="w-64 h-64 object-cover rounded-lg shadow-2xl">
            @endif
            <div class="text-center md:text-left">
                <span class="text-mw-red text-xs font-heading uppercase tracking-wider">{{ strtoupper($latestRelease->type) }}</span>
                <h3 class="font-display text-2xl md:text-3xl uppercase mt-1 mb-2">{{ $latestRelease->title }}</h3>
                @if ($latestRelease->release_date)
                    <p class="text-gray-400 text-sm mb-4">{{ $latestRelease->release_date->translatedFormat('d F Y') }}</p>
                @endif

                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                    @if ($latestRelease->spotify_url)
                        <a href="{{ $latestRelease->spotify_url }}" target="_blank" class="px-4 py-2 bg-[#1DB954]/20 text-[#1DB954] hover:bg-[#1DB954] hover:text-black text-xs font-heading uppercase rounded transition-all">Spotify</a>
                    @endif
                    @if ($latestRelease->bandcamp_url)
                        <a href="{{ $latestRelease->bandcamp_url }}" target="_blank" class="px-4 py-2 bg-[#629aa9]/20 text-[#629aa9] hover:bg-[#629aa9] hover:text-black text-xs font-heading uppercase rounded transition-all">Bandcamp</a>
                    @endif
                    @if ($latestRelease->apple_music_url)
                        <a href="{{ $latestRelease->apple_music_url }}" target="_blank" class="px-4 py-2 bg-[#fc3c44]/20 text-[#fc3c44] hover:bg-[#fc3c44] hover:text-white text-xs font-heading uppercase rounded transition-all">Apple Music</a>
                    @endif
                    @if ($latestRelease->deezer_url)
                        <a href="{{ $latestRelease->deezer_url }}" target="_blank" class="px-4 py-2 bg-[#a238ff]/20 text-[#a238ff] hover:bg-[#a238ff] hover:text-white text-xs font-heading uppercase rounded transition-all">Deezer</a>
                    @endif
                </div>

                <a href="{{ route('discography') }}" class="inline-block mt-6 text-gray-400 hover:text-white font-heading uppercase text-sm tracking-wider transition-colors">
                    Toute la discographie &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endif

@endsection
