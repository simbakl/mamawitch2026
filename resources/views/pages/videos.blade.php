@extends('layouts.app')
@section('title', 'Vidéos')
@section('meta_description', 'Clips, lives et sessions vidéo de Mama Witch, groupe de Hard Rock à Paris.')

@php
    $categoryLabels = ['clip' => 'Clips', 'live' => 'Live', 'session' => 'Sessions', 'interview' => 'Interviews', 'other' => 'Autres'];
    $categories = $videos->pluck('category')->unique()->filter();
@endphp

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-5xl mx-auto" x-data="{ filter: 'all' }">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-12">Vidéos</h1>

        {{-- Category filters --}}
        @if ($categories->count() > 1)
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <button @click="filter = 'all'"
                    :class="filter === 'all' ? 'bg-mw-red text-white' : 'bg-mw-dark text-gray-400 hover:text-white border border-white/10'"
                    class="px-4 py-1.5 text-xs font-heading uppercase tracking-wider rounded transition-all cursor-pointer">
                    Tout
                </button>
                @foreach ($categories as $cat)
                    <button @click="filter = '{{ $cat }}'"
                        :class="filter === '{{ $cat }}' ? 'bg-mw-red text-white' : 'bg-mw-dark text-gray-400 hover:text-white border border-white/10'"
                        class="px-4 py-1.5 text-xs font-heading uppercase tracking-wider rounded transition-all cursor-pointer">
                        {{ $categoryLabels[$cat] ?? ucfirst($cat) }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="space-y-8">
            @forelse ($videos as $video)
                @php
                    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video->youtube_url, $matches);
                    $youtubeId = $matches[1] ?? null;
                @endphp
                @if ($youtubeId)
                    <div x-show="filter === 'all' || filter === '{{ $video->category }}'" x-transition class="bg-mw-dark rounded-lg overflow-hidden border border-white/5">
                        <div class="aspect-video">
                            <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" title="{{ $video->title }}" loading="lazy" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <h3 class="font-heading uppercase">{{ $video->title }}</h3>
                                @if ($video->published_at)
                                    <p class="text-gray-500 text-xs">{{ $video->published_at->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            <span class="px-3 py-1 text-xs font-heading uppercase rounded
                                {{ match($video->category) {
                                    'clip' => 'bg-red-500/20 text-red-400',
                                    'live' => 'bg-green-500/20 text-green-400',
                                    'session' => 'bg-amber-500/20 text-amber-400',
                                    'interview' => 'bg-blue-500/20 text-blue-400',
                                    default => 'bg-gray-500/20 text-gray-400',
                                } }}">
                                {{ $categoryLabels[$video->category] ?? ucfirst($video->category) }}
                            </span>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-center text-gray-500 py-12">Aucune vidéo pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
