@extends('layouts.app')
@section('title', $page->title)

@section('content')
<div class="pt-24 pb-20 px-4">
    <div class="max-w-3xl mx-auto">
        <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mb-8">{{ $page->title }}</h1>

        <div class="prose prose-invert prose-red max-w-none">
            {!! $page->body !!}
        </div>
    </div>
</div>
@endsection
