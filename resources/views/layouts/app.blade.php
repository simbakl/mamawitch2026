<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mama Witch') - Hard Rock Paris</title>
    <meta name="description" content="@yield('meta_description', App\Models\SiteSetting::get('meta_description', 'Mama Witch - Groupe de Hard Rock - Paris'))">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Mama Witch')">
    <meta property="og:description" content="@yield('meta_description', 'Mama Witch - Groupe de Hard Rock - Paris')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-white.png'))">
    <meta property="og:type" content="website">

    <link rel="icon" href="{{ asset('images/logo-icon-white.svg') }}" type="image/svg+xml">

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
