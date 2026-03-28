<nav x-data="{ open: false }" class="fixed top-0 w-full z-50 bg-mw-black/90 backdrop-blur-sm border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex-shrink-0">
                <img src="{{ asset('images/logo-white.png') }}" alt="Mama Witch" class="h-8">
            </a>

            {{-- Desktop Menu --}}
            <div class="hidden md:flex items-center space-x-1">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="px-3 py-2 text-sm font-heading uppercase tracking-wider transition-colors duration-200
                              {{ request()->routeIs($item['route'] . '*') ? 'text-mw-red' : 'text-gray-300 hover:text-white' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach

                @foreach ($staticMenuPages as $menuPage)
                    <a href="{{ url('/' . $menuPage->slug) }}"
                       class="px-3 py-2 text-sm font-heading uppercase tracking-wider transition-colors duration-200
                              {{ request()->is($menuPage->slug) ? 'text-mw-red' : 'text-gray-300 hover:text-white' }}">
                        {{ $menuPage->title }}
                    </a>
                @endforeach

                @if ($isProActive)
                    <a href="{{ auth()->check() && auth()->user()->hasRole('pro') ? route('pro.dashboard') : route('pro.request') }}"
                       class="ml-4 px-4 py-1.5 text-xs font-heading uppercase tracking-wider border border-mw-red text-mw-red hover:bg-mw-red hover:text-white transition-all duration-200 rounded">
                        Espace Pro
                    </a>
                @endif
            </div>

            {{-- Mobile menu button --}}
            <button @click="open = !open" aria-label="Menu" :aria-expanded="open" class="md:hidden text-gray-300 hover:text-white cursor-pointer">
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak x-transition class="md:hidden bg-mw-dark border-t border-white/5">
        <div class="px-4 py-4 space-y-2">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="block px-3 py-2 font-heading uppercase tracking-wider text-sm
                          {{ request()->routeIs($item['route'] . '*') ? 'text-mw-red' : 'text-gray-300 hover:text-white' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            @foreach ($staticMenuPages as $menuPage)
                <a href="{{ url('/' . $menuPage->slug) }}"
                   class="block px-3 py-2 font-heading uppercase tracking-wider text-sm
                          {{ request()->is($menuPage->slug) ? 'text-mw-red' : 'text-gray-300 hover:text-white' }}">
                    {{ $menuPage->title }}
                </a>
            @endforeach
            @if ($isProActive)
                <a href="{{ auth()->check() && auth()->user()->hasRole('pro') ? route('pro.dashboard') : route('pro.request') }}"
                   class="block px-3 py-2 font-heading uppercase tracking-wider text-sm text-mw-red">
                    Espace Pro
                </a>
            @endif
        </div>
    </div>
</nav>
