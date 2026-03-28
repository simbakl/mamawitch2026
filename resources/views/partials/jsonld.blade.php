{{-- Global MusicGroup schema --}}
@php
    $sameAs = array_filter([
        $socialLinks['facebook'] ?? null,
        $socialLinks['instagram'] ?? null,
        $socialLinks['youtube'] ?? null,
        $socialLinks['twitter'] ?? null,
    ]);
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'MusicGroup',
    'name' => 'Mama Witch',
    'genre' => 'Hard Rock',
    'foundingLocation' => [
        '@type' => 'Place',
        'name' => 'Paris, France',
    ],
    'url' => url('/'),
    'logo' => asset('images/logo-white.png'),
    'sameAs' => array_values($sameAs),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@stack('jsonld')
