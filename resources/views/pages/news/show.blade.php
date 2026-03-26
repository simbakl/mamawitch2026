@extends('layouts.app')
@section('title', $article->title)

@section('content')
<div class="pt-24 pb-20 px-4">
    <article class="max-w-3xl mx-auto">
        {{-- Category + date --}}
        <div class="flex items-center gap-3 mb-4">
            @if ($article->category)
                <span class="text-mw-red text-xs font-heading uppercase tracking-wider">{{ $article->category->name }}</span>
            @endif
            <span class="text-gray-600 text-sm">{{ ($article->published_at ?? $article->created_at)->format('d/m/Y') }}</span>
        </div>

        <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mb-6">{{ $article->title }}</h1>

        {{-- Featured image --}}
        @if ($article->featured_image)
            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full rounded-lg mb-8">
        @endif

        {{-- Excerpt --}}
        <p class="text-lg text-gray-300 mb-8 leading-relaxed">{{ $article->excerpt }}</p>

        {{-- Body --}}
        @if ($article->body)
            <div class="prose prose-invert prose-red max-w-none">
                {!! $article->body !!}
            </div>
        @endif

        {{-- YouTube embed --}}
        @if ($article->youtube_url)
            @php
                preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $article->youtube_url, $matches);
                $youtubeId = $matches[1] ?? null;
            @endphp
            @if ($youtubeId)
                <div class="mt-8 aspect-video rounded-lg overflow-hidden">
                    <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                </div>
            @endif
        @endif

        {{-- Back link --}}
        <div class="mt-12 pt-6 border-t border-white/10">
            <a href="{{ route('news.index') }}" class="text-gray-400 hover:text-white font-heading uppercase text-sm tracking-wider transition-colors">
                &larr; Retour aux actus
            </a>
        </div>
    </article>
</div>
@endsection
