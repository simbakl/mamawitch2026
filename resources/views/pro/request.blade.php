@extends('layouts.app')

@section('title', 'Accès Pro')

@section('content')
<section class="py-20 px-4 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-12">
            <span class="text-mw-red text-xs font-heading uppercase tracking-wider">Espace Professionnel</span>
            <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mt-2">Demande d'accès</h1>
            <p class="text-gray-400 mt-4 max-w-lg mx-auto">
                Vous êtes programmateur, journaliste ou label ? Demandez un accès à notre espace pro pour consulter nos documents et contenus exclusifs.
            </p>
        </div>

        @if (session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg p-6 text-center mb-8">
                <svg class="w-8 h-8 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="font-heading uppercase tracking-wider">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg p-4 mb-8">
                {{ session('error') }}
            </div>
        @endif

        @if (! session('success'))
        <form method="POST" action="{{ route('pro.request.submit') }}" class="space-y-6">
            @csrf
            <input type="text" name="honeypot" class="hidden" value="">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Prénom *</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                        class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red transition-colors">
                    @error('first_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Nom *</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                        class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red transition-colors">
                    @error('last_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="votre@email.com"
                    class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red transition-colors">
                <p class="text-gray-600 text-xs mt-1">Cet email servira pour vous connecter à l'Espace Pro.</p>
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="structure" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Structure / Média *</label>
                <input type="text" name="structure" id="structure" value="{{ old('structure') }}" required placeholder="Nom de votre salle, média, label..."
                    class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red transition-colors">
                @error('structure') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="pro_type_id" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Profil *</label>
                <select name="pro_type_id" id="pro_type_id" required
                    class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white focus:border-mw-red focus:ring-1 focus:ring-mw-red transition-colors">
                    <option value="">Sélectionnez votre profil</option>
                    @foreach ($proTypes as $type)
                        <option value="{{ $type->id }}" {{ old('pro_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('pro_type_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Message (optionnel)</label>
                <textarea name="message" id="message" rows="4" placeholder="Précisez votre demande..."
                    class="w-full bg-mw-dark border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red transition-colors resize-none">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="text-center pt-4">
                <button type="submit" class="inline-block px-8 py-3 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-sm transition-all duration-300 rounded">
                    Envoyer ma demande
                </button>
            </div>
        </form>
        @endif

        {{-- Already have access? --}}
        <div class="text-center mt-12 pt-8 border-t border-white/5">
            <p class="text-gray-500 text-sm mb-4">Vous avez déjà un accès ?</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/admin/login') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-mw-red hover:bg-mw-red-dark border border-mw-red rounded-lg text-sm font-heading uppercase tracking-wider text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Se connecter
                </a>
                <a href="{{ url('/auth/google') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-sm font-heading uppercase tracking-wider transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Connexion Google
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
