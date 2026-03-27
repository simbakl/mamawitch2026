<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Espace Pro') - Mama Witch</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" href="{{ asset('images/logo-icon-white.svg') }}" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('partials.analytics')
</head>
<body class="bg-mw-black text-white font-sans antialiased">

    {{-- Pro Navigation --}}
    <nav class="fixed top-0 w-full z-50 bg-mw-black/90 backdrop-blur-sm border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex-shrink-0">
                        <img src="{{ asset('images/logo-white.png') }}" alt="Mama Witch" class="h-8">
                    </a>
                    <span class="hidden sm:inline-block px-2 py-0.5 bg-mw-red/20 text-mw-red text-xs font-heading uppercase tracking-wider rounded">Espace Pro</span>
                </div>

                @auth
                    <div class="flex items-center gap-4">
                        <a href="{{ route('pro.dashboard') }}" class="text-sm font-heading uppercase tracking-wider text-gray-300 hover:text-white transition-colors">
                            Dashboard
                        </a>
                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            @if (auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="" class="w-6 h-6 rounded-full">
                            @endif
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-white text-sm transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <main class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-mw-dark border-t border-white/5 mt-20">
        <div class="max-w-7xl mx-auto px-4 py-8 text-center text-xs text-gray-600">
            <p>&copy; {{ date('Y') }} Mama Witch — Espace Professionnel</p>
            <p class="mt-1">
                <a href="{{ route('home') }}" class="hover:text-gray-400 transition-colors">Retour au site</a>
            </p>
        </div>
    </footer>

</body>
</html>
