<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mama Witch') - Hard Rock Paris</title>
    @if (str_contains(config('app.url'), 'preprod'))
        <meta name="robots" content="noindex, nofollow">
    @endif
    <meta name="description" content="@yield('meta_description', $defaultMetaDescription)">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Mama Witch')">
    <meta property="og:description" content="@yield('meta_description', $defaultMetaDescription)">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-white.png'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Mama Witch">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Mama Witch')">
    <meta name="twitter:description" content="@yield('meta_description', $defaultMetaDescription)">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo-white.png'))">

    <link rel="icon" href="{{ asset('images/logo-icon-white.svg') }}" type="image/svg+xml">
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mw-black text-white font-sans antialiased">

    {{-- Navigation --}}
    @include('partials.nav')

    {{-- Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

</body>
</html>
