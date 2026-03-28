@extends('layouts.app')
@section('title', $gallery->title)
@section('meta_description', $gallery->description ?? 'Galerie photo - ' . $gallery->title)
@if ($gallery->cover_photo)
    @section('og_image', asset('storage/' . $gallery->cover_photo))
@endif

@section('content')
<div class="pt-24 pb-20 px-4" x-data="lightbox()">
    <div class="max-w-6xl mx-auto">
        <a href="{{ route('gallery.index') }}" class="text-gray-400 hover:text-white font-heading uppercase text-sm tracking-wider transition-colors">
            &larr; Retour aux galeries
        </a>

        <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mt-4 mb-2">{{ $gallery->title }}</h1>
        @if ($gallery->description)
            <p class="text-gray-400 mb-8">{{ $gallery->description }}</p>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach ($gallery->photos as $index => $photo)
                <button @click="open({{ $index }})" class="group aspect-square rounded overflow-hidden cursor-pointer focus:outline-none focus:ring-2 focus:ring-mw-red">
                    <img src="{{ asset('storage/' . $photo->image) }}"
                         alt="{{ $photo->caption ?? $gallery->title . ' - Photo ' . ($index + 1) }}"
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </button>
            @endforeach
        </div>
    </div>

    {{-- Lightbox Modal --}}
    <div x-show="isOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="close()"
         @keydown.arrow-left.window="prev()"
         @keydown.arrow-right.window="next()"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75"
         @click.self="close()">

        {{-- Close --}}
        <button @click="close()" class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors z-10 p-2 cursor-pointer" aria-label="Fermer">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Counter --}}
        <div class="absolute top-4 left-4 text-white/60 text-sm font-heading">
            <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
        </div>

        {{-- Prev --}}
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors p-2 cursor-pointer" aria-label="Photo précédente">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        {{-- Image --}}
        <img :src="images[currentIndex]?.src" :alt="images[currentIndex]?.alt"
             class="max-h-[85vh] max-w-[90vw] object-contain rounded select-none"
             @click.stop>

        {{-- Next --}}
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/60 hover:text-white transition-colors p-2 cursor-pointer" aria-label="Photo suivante">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- Caption --}}
        <div x-show="images[currentIndex]?.alt" class="absolute bottom-6 left-0 right-0 text-center text-white/70 text-sm">
            <span x-text="images[currentIndex]?.alt"></span>
        </div>
    </div>
</div>

@php
    $lightboxPhotos = $gallery->photos->map(function ($p) {
        return ['src' => asset('storage/' . $p->image), 'alt' => $p->caption ?? ''];
    })->values();
@endphp
<script>
function lightbox() {
    const photos = @json($lightboxPhotos);

    return {
        isOpen: false,
        currentIndex: 0,
        images: photos,
        open(index) { this.currentIndex = index; this.isOpen = true; },
        close() { this.isOpen = false; },
        next() { this.currentIndex = (this.currentIndex + 1) % this.images.length; },
        prev() { this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length; },
    };
}
</script>
@endsection
