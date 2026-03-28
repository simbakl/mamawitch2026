@extends('layouts.app')
@section('title', 'Le Groupe')
@section('meta_description', 'Découvrez les membres de Mama Witch, groupe de Hard Rock à Paris.')

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-6xl mx-auto">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-16">Le Groupe</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($members as $member)
                <div class="bg-mw-dark rounded-lg overflow-hidden border border-white/5 group">
                    {{-- Photo --}}
                    <div class="aspect-[3/4] overflow-hidden">
                        @if ($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-mw-gray flex items-center justify-center">
                                <span class="text-6xl font-display text-gray-600">{{ substr($member->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="font-display text-xl uppercase">{{ $member->name }}</h3>
                        <p class="text-mw-red font-heading uppercase text-sm tracking-wider mt-1">{{ $member->instruments }}</p>

                        @if ($member->bio)
                            <div x-data="{ expanded: false }">
                                <p class="text-gray-400 text-sm mt-3 leading-relaxed" x-show="!expanded">
                                    {{ Str::limit($member->bio, 150) }}
                                </p>
                                <p class="text-gray-400 text-sm mt-3 leading-relaxed" x-show="expanded" x-cloak>
                                    {{ $member->bio }}
                                </p>
                                @if (Str::length($member->bio) > 150)
                                    <button @click="expanded = !expanded" class="text-mw-red text-xs font-heading uppercase tracking-wider mt-2 hover:text-white transition-colors cursor-pointer">
                                        <span x-show="!expanded">Lire la suite</span>
                                        <span x-show="expanded">Réduire</span>
                                    </button>
                                @endif
                            </div>
                        @endif

                        @if ($member->hasSocialLinks())
                            <div class="flex gap-3 mt-4">
                                @if ($member->instagram)
                                    <a href="{{ $member->instagram }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-white transition-colors" title="Instagram">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                @endif
                                @if ($member->facebook)
                                    <a href="{{ $member->facebook }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-white transition-colors" title="Facebook">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                @endif
                                @if ($member->twitter)
                                    <a href="{{ $member->twitter }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-white transition-colors" title="X (Twitter)">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    </a>
                                @endif
                                @if ($member->youtube)
                                    <a href="{{ $member->youtube }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-white transition-colors" title="YouTube">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    </a>
                                @endif
                                @if ($member->website)
                                    <a href="{{ $member->website }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-white transition-colors" title="Site web">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 3a15.3 15.3 0 0 1 4 9 15.3 15.3 0 0 1-4 9 15.3 15.3 0 0 1-4-9 15.3 15.3 0 0 1 4-9z"/></svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
