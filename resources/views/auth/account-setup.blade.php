<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurer mon compte - Mama Witch</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-mw-black text-white font-sans antialiased min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo-white.png') }}" alt="Mama Witch" class="h-12 mx-auto mb-4">
            <h1 class="font-heading text-xl uppercase tracking-wider">Configurer mon compte</h1>
            <p class="text-gray-400 text-sm mt-2">Bienvenue <strong class="text-white">{{ $user->name }}</strong></p>
        </div>

        {{-- Error messages --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-400 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Password form --}}
        <form method="POST" action="{{ url('/account/setup/' . $token) }}" class="bg-mw-dark border border-white/5 rounded-lg p-8">
            @csrf

            <div class="mb-5">
                <label for="password" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Nouveau mot de passe</label>
                <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password"
                       class="w-full px-4 py-3 bg-mw-black border border-white/10 rounded-lg text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red focus:outline-none transition-colors"
                       placeholder="8 caractères minimum">
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password"
                       class="w-full px-4 py-3 bg-mw-black border border-white/10 rounded-lg text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red focus:outline-none transition-colors">
            </div>

            <button type="submit"
                    class="w-full px-6 py-3 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-sm rounded-lg transition-colors cursor-pointer">
                Définir mon mot de passe
            </button>
        </form>

        {{-- Separator --}}
        <div class="flex items-center gap-4 my-6">
            <div class="flex-1 h-px bg-white/10"></div>
            <span class="text-sm text-gray-500">ou</span>
            <div class="flex-1 h-px bg-white/10"></div>
        </div>

        {{-- Google SSO --}}
        <a href="{{ url('/auth/google') }}"
           class="flex items-center justify-center gap-3 w-full px-4 py-3 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-lg transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span class="text-sm font-medium text-gray-300">Se connecter avec Google</span>
        </a>

        <p class="text-center text-gray-600 text-xs mt-6">Ce lien est valable 48 heures.</p>
    </div>
</body>
</html>
