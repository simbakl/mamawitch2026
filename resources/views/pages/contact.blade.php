@extends('layouts.app')
@section('title', 'Contact')
@section('meta_description', 'Contactez Mama Witch pour booking, presse ou toute demande.')

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-2xl mx-auto">
        <h1 class="font-display text-4xl md:text-5xl uppercase tracking-wider text-center mb-4">Contact</h1>
        <p class="text-gray-400 text-center mb-12">Une question, une demande de booking, un message ? Ecrivez-nous.</p>

        @if (session('success'))
            <div class="bg-green-500/20 border border-green-500/30 text-green-400 rounded-lg p-4 mb-8 text-center">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
            @csrf
            {{-- Honeypot --}}
            <input type="text" name="honeypot" value="" class="hidden">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Nom *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red outline-none transition-colors">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red outline-none transition-colors">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="subject" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Objet</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                       class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red outline-none transition-colors">
            </div>

            <div>
                <label for="message" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Message *</label>
                <textarea name="message" id="message" rows="6" required
                          class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red outline-none transition-colors resize-none">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="text-center">
                <button type="submit" class="px-8 py-3 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-sm rounded transition-all duration-300">
                    Envoyer le message
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
