@extends('layouts.app')
@section('title', $gallery->title)

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-6xl mx-auto">
        <a href="{{ route('gallery.index') }}" class="text-gray-400 hover:text-white font-heading uppercase text-sm tracking-wider transition-colors">
            &larr; Retour aux galeries
        </a>

        <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mt-4 mb-2">{{ $gallery->title }}</h1>
        @if ($gallery->description)
            <p class="text-gray-400 mb-8">{{ $gallery->description }}</p>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach ($gallery->photos as $photo)
                <a href="{{ asset('storage/' . $photo->image) }}" target="_blank" class="group aspect-square rounded overflow-hidden">
                    <img src="{{ asset('storage/' . $photo->image) }}" alt="{{ $photo->caption ?? '' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
