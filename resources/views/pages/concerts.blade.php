@extends('layouts.app')
@section('title', 'Concerts')
@section('meta_description', 'Dates de concerts et événements de Mama Witch, groupe de Hard Rock à Paris.')

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-5xl mx-auto">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-16">Concerts</h1>

        {{-- Upcoming --}}
        @if ($upcoming->count())
            <h2 class="font-heading uppercase text-lg tracking-wider text-mw-red mb-6">Prochaines dates</h2>
            <div class="space-y-4 mb-16">
                @foreach ($upcoming as $concert)
                    @php $hasDetails = $concert->poster || $concert->description; @endphp
                    <div x-data="{ open: false }" class="bg-mw-dark rounded-lg border border-white/5 hover:border-mw-red/30 transition-colors overflow-hidden">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 {{ $hasDetails ? 'cursor-pointer' : '' }}" @if($hasDetails) @click="open = !open" @endif>
                            <div class="flex items-center gap-4">
                                <div class="text-center min-w-[60px]">
                                    <div class="text-2xl font-display text-mw-red">{{ $concert->date->format('d') }}</div>
                                    <div class="text-xs font-heading uppercase text-gray-400">{{ $concert->date->translatedFormat('M Y') }}</div>
                                </div>
                                <div>
                                    <h3 class="font-heading uppercase text-lg">{{ $concert->title }}</h3>
                                    <p class="text-gray-400 text-sm">{{ $concert->venue }} — {{ $concert->city }}</p>
                                    @if ($concert->address)
                                        <p class="text-gray-500 text-xs">{{ $concert->address }}{{ $concert->postal_code ? ', ' . $concert->postal_code : '' }} {{ $concert->city }}</p>
                                    @endif
                                    <p class="text-gray-500 text-xs mt-1">{{ $concert->date->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-3 md:mt-0">
                                @if ($concert->status === 'soldout')
                                    <span class="px-3 py-1 bg-mw-amber/20 text-mw-amber text-xs font-heading uppercase rounded">Complet</span>
                                @elseif ($concert->status === 'cancelled')
                                    <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-heading uppercase rounded line-through">Annulé</span>
                                @elseif ($concert->ticket_url)
                                    <a href="{{ $concert->ticket_url }}" target="_blank" @click.stop class="px-5 py-2 bg-mw-red hover:bg-mw-red-dark text-white text-xs font-heading uppercase tracking-wider rounded transition-colors">
                                        Billets
                                    </a>
                                @endif
                                @if ($hasDetails)
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-300" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        {{-- Details panel --}}
                        @if ($hasDetails)
                            <div x-show="open" x-collapse x-cloak>
                                <div class="px-5 pb-5 pt-2 border-t border-white/5" x-data="{ zoomed: false }">
                                    <div class="flex flex-col md:flex-row gap-6">
                                        @if ($concert->poster)
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('storage/' . $concert->poster) }}" alt="Affiche {{ $concert->title }}" loading="lazy"
                                                     class="w-48 rounded-lg shadow-lg cursor-pointer hover:opacity-80 transition-opacity" @click.stop="zoomed = true">

                                                {{-- Lightbox --}}
                                                <div x-show="zoomed" x-transition.opacity @click.self="zoomed = false" @keydown.escape.window="zoomed = false"
                                                     class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4" style="display: none;">
                                                    <button @click.stop="zoomed = false" class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors p-2 cursor-pointer">
                                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                    <img src="{{ asset('storage/' . $concert->poster) }}" alt="Affiche {{ $concert->title }}" class="max-w-full max-h-[90vh] object-contain rounded-lg" @click.stop>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($concert->description)
                                            <div class="flex-1">
                                                <div class="text-gray-400 text-sm leading-relaxed prose prose-sm prose-invert prose-p:my-1 prose-headings:text-white prose-a:text-mw-red max-w-none">{!! $concert->description !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 mb-16">Aucune date à venir pour le moment. Restez connectés !</p>
        @endif

        {{-- Past --}}
        @if ($past->count())
            <h2 class="font-heading uppercase text-lg tracking-wider text-gray-500 mb-6">Dates passées</h2>
            <div class="space-y-3 opacity-60">
                @foreach ($past as $concert)
                    @php $hasDetails = $concert->poster || $concert->description; @endphp
                    <div x-data="{ open: false }" class="bg-mw-dark/50 rounded-lg border border-white/5 overflow-hidden">
                        <div class="flex items-center justify-between p-4 {{ $hasDetails ? 'cursor-pointer' : '' }}" @if($hasDetails) @click="open = !open" @endif>
                            <div class="flex items-center gap-4">
                                <div class="text-center min-w-[60px]">
                                    <div class="text-lg font-display text-gray-500">{{ $concert->date->format('d') }}</div>
                                    <div class="text-xs font-heading uppercase text-gray-600">{{ $concert->date->translatedFormat('M Y') }}</div>
                                </div>
                                <div>
                                    <h3 class="font-heading uppercase text-gray-400">{{ $concert->title }}</h3>
                                    <p class="text-gray-500 text-sm">{{ $concert->venue }} — {{ $concert->city }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-600 font-heading uppercase">Passé</span>
                                @if ($hasDetails)
                                    <svg class="w-4 h-4 text-gray-600 transition-transform duration-300" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        @if ($hasDetails)
                            <div x-show="open" x-collapse x-cloak>
                                <div class="px-4 pb-4 pt-2 border-t border-white/5" x-data="{ zoomed: false }">
                                    <div class="flex flex-col md:flex-row gap-6">
                                        @if ($concert->poster)
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('storage/' . $concert->poster) }}" alt="Affiche {{ $concert->title }}" loading="lazy"
                                                     class="w-40 rounded-lg shadow-lg cursor-pointer hover:opacity-80 transition-opacity" @click.stop="zoomed = true">

                                                <div x-show="zoomed" x-transition.opacity @click.self="zoomed = false" @keydown.escape.window="zoomed = false"
                                                     class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4" style="display: none;">
                                                    <button @click.stop="zoomed = false" class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors p-2 cursor-pointer">
                                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                    <img src="{{ asset('storage/' . $concert->poster) }}" alt="Affiche {{ $concert->title }}" class="max-w-full max-h-[90vh] object-contain rounded-lg" @click.stop>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($concert->description)
                                            <div class="flex-1">
                                                <div class="text-gray-400 text-sm leading-relaxed prose prose-sm prose-invert prose-p:my-1 prose-headings:text-white prose-a:text-mw-red max-w-none">{!! $concert->description !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
