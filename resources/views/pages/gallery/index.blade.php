@extends('layouts.app')
@section('title', 'Galerie Photos')
@section('meta_description', 'Photos de concerts, backstage et sessions studio de Mama Witch.')

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-6xl mx-auto">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-16">Galerie</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($galleries as $gallery)
                <a href="{{ route('gallery.show', $gallery->slug) }}" class="group relative aspect-square rounded-lg overflow-hidden">
                    @if ($gallery->photos->first())
                        <img src="{{ asset('storage/' . $gallery->photos->first()->image) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full bg-mw-gray"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent group-hover:from-mw-red/80 transition-all duration-500"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-5">
                        <h3 class="font-heading uppercase text-lg">{{ $gallery->title }}</h3>
                        <p class="text-gray-400 text-sm">{{ $gallery->photos_count }} photos</p>
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center text-gray-500 py-12">Aucune galerie pour le moment.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
