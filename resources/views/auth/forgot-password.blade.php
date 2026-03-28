<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié - Mama Witch</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-mw-black text-white font-sans antialiased min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo-white.png') }}" alt="Mama Witch" class="h-12 mx-auto mb-4">
            <h1 class="font-heading text-xl uppercase tracking-wider">Mot de passe oublié</h1>
            <p class="text-gray-400 text-sm mt-2">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                <p class="text-green-400 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Error messages --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-400 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="bg-mw-dark border border-white/5 rounded-lg p-8">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-sm font-heading uppercase tracking-wider text-gray-400 mb-2">Adresse email</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" autocomplete="email"
                       class="w-full px-4 py-3 bg-mw-black border border-white/10 rounded-lg text-white placeholder-gray-600 focus:border-mw-red focus:ring-1 focus:ring-mw-red focus:outline-none transition-colors"
                       placeholder="email@exemple.com">
            </div>

            <button type="submit"
                    class="w-full px-6 py-3 bg-mw-red hover:bg-mw-red-dark text-white font-heading uppercase tracking-wider text-sm rounded-lg transition-colors cursor-pointer">
                Envoyer le lien
            </button>
        </form>

        <p class="text-center mt-6">
            <a href="{{ url('/admin/login') }}" class="text-gray-400 hover:text-white text-sm transition-colors">
                &larr; Retour à la connexion
            </a>
        </p>
    </div>
</body>
</html>
