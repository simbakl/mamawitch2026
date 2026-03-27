@extends('layouts.app')
@section('title', 'Vidéos')
@section('meta_description', 'Clips, lives et sessions vidéo de Mama Witch, groupe de Hard Rock à Paris.')

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-5xl mx-auto">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-16">Vidéos</h1>

        <div class="space-y-8">
            @forelse ($videos as $video)
                @php
                    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video->youtube_url, $matches);
                    $youtubeId = $matches[1] ?? null;
                @endphp
                @if ($youtubeId)
                    <div class="bg-mw-dark rounded-lg overflow-hidden border border-white/5">
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
                                {{ match($video->category) { 'clip' => 'Clip', 'live' => 'Live', 'session' => 'Session', 'interview' => 'Interview', default => 'Autre' } }}
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
