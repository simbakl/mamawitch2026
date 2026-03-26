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
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between bg-mw-dark rounded-lg p-5 border border-white/5 hover:border-mw-red/30 transition-colors">
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
                                <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-heading uppercase rounded line-through">Annule</span>
                            @elseif ($concert->ticket_url)
                                <a href="{{ $concert->ticket_url }}" target="_blank" class="px-5 py-2 bg-mw-red hover:bg-mw-red-dark text-white text-xs font-heading uppercase tracking-wider rounded transition-colors">
                                    Billets
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 mb-16">Aucune date a venir pour le moment. Restez connectes !</p>
        @endif

        {{-- Past --}}
        @if ($past->count())
            <h2 class="font-heading uppercase text-lg tracking-wider text-gray-500 mb-6">Dates passees</h2>
            <div class="space-y-3 opacity-60">
                @foreach ($past as $concert)
                    <div class="flex items-center justify-between bg-mw-dark/50 rounded-lg p-4 border border-white/5">
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
                        <span class="text-xs text-gray-600 font-heading uppercase">Passe</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
