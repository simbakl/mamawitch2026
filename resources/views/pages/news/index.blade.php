@extends('layouts.app')
@section('title', 'Actualités')
@section('meta_description', 'Toutes les actualités de Mama Witch, groupe de Hard Rock à Paris.')

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-6xl mx-auto">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-8">Actualités</h1>

        {{-- Categories filter --}}
        <div class="flex flex-wrap justify-center gap-2 mb-12">
            <a href="{{ route('news.index') }}"
               class="px-4 py-1.5 text-xs font-heading uppercase tracking-wider rounded transition-all
                      {{ !isset($category) ? 'bg-mw-red text-white' : 'bg-mw-dark text-gray-400 hover:text-white border border-white/10' }}">
                Tout
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('news.category', $cat->slug) }}"
                   class="px-4 py-1.5 text-xs font-heading uppercase tracking-wider rounded transition-all
                          {{ (isset($category) && $category->id === $cat->id) ? 'bg-mw-red text-white' : 'bg-mw-dark text-gray-400 hover:text-white border border-white/10' }}">
                    {{ $cat->name }} ({{ $cat->news_count }})
                </a>
            @endforeach
        </div>

        {{-- Articles grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse ($news as $article)
                <article class="bg-mw-dark rounded-lg overflow-hidden border border-white/5 hover:border-mw-red/30 transition-colors group">
                    @if ($article->featured_image)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    @endif
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            @if ($article->category)
                                <span class="text-mw-red text-xs font-heading uppercase tracking-wider">{{ $article->category->name }}</span>
                            @endif
                            <span class="text-gray-600 text-xs">{{ ($article->published_at ?? $article->created_at)->format('d/m/Y') }}</span>
                        </div>
                        <h3 class="font-heading uppercase text-lg mb-2 group-hover:text-mw-red transition-colors">{{ $article->title }}</h3>
                        <p class="text-gray-400 text-sm line-clamp-3">{{ $article->excerpt }}</p>
                        @if ($article->hasDetailPage())
                            <a href="{{ route('news.show', $article->slug) }}" class="inline-block mt-3 text-mw-red text-sm font-heading uppercase tracking-wider hover:text-white transition-colors">
                                Lire la suite &rarr;
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center text-gray-500 py-12">Aucune actualité pour le moment.</div>
            @endforelse
        </div>

        {{ $news->links() }}
    </div>
</div>
@endsection
