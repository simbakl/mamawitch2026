@extends('layouts.pro')

@section('title', $project->title)

@section('content')
<section class="py-20 px-4">
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('pro.dashboard') }}" class="text-gray-500 hover:text-white text-sm font-heading uppercase tracking-wider transition-colors">
                &larr; Dashboard
            </a>
        </div>

        <div class="mb-10">
            <span class="text-mw-amber text-xs font-heading uppercase tracking-wider">Écoute privée</span>
            <h1 class="font-display text-3xl md:text-4xl uppercase tracking-wider mt-2">{{ $project->title }}</h1>
            @if ($project->description)
                <p class="text-gray-400 mt-3">{{ $project->description }}</p>
            @endif
        </div>

        {{-- Track list with secure player --}}
        <div class="space-y-3" x-data="proPlayer()" @contextmenu.prevent>

            @foreach ($project->tracks as $track)
                @php
                    $infoUrl = route('pro.audio.info', ['track' => $track->id]);
                @endphp
                <div class="bg-mw-dark rounded-lg border border-white/5 hover:border-mw-amber/20 transition-colors overflow-hidden"
                     :class="{ 'border-mw-amber/40': currentTrack === {{ $track->id }} }">
                    <div class="flex items-center gap-4 p-4">
                        {{-- Play/Pause --}}
                        <button
                            @click="togglePlay({{ $track->id }}, '{{ $infoUrl }}')"
                            :disabled="isLoading && currentTrack === {{ $track->id }}"
                            class="w-10 h-10 bg-mw-amber/10 hover:bg-mw-amber/20 rounded-full flex items-center justify-center flex-shrink-0 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-wait">
                            <template x-if="isLoading && currentTrack === {{ $track->id }}">
                                <svg class="w-5 h-5 text-mw-amber animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <template x-if="!(isLoading && currentTrack === {{ $track->id }}) && currentTrack === {{ $track->id }} && isPlaying">
                                <svg class="w-5 h-5 text-mw-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"/>
                                </svg>
                            </template>
                            <template x-if="!(isLoading && currentTrack === {{ $track->id }}) && (currentTrack !== {{ $track->id }} || !isPlaying)">
                                <svg class="w-5 h-5 text-mw-amber ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </template>
                        </button>

                        {{-- Track info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-heading uppercase text-sm truncate">{{ $track->title }}</h3>
                                @if ($track->duration)
                                    <span class="text-gray-500 text-xs ml-3 flex-shrink-0">{{ $track->duration }}</span>
                                @endif
                            </div>

                            {{-- Progress bar --}}
                            <div x-show="currentTrack === {{ $track->id }}"
                                 x-transition
                                 class="mt-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400 w-10 text-right tabular-nums" x-text="formatTime(currentTime)">0:00</span>
                                    <div class="flex-1 relative py-2 cursor-pointer"
                                         @mousedown="startDrag($event)"
                                         @mousemove.window="onDrag($event)"
                                         @mouseup.window="stopDrag()"
                                         @click="seek($event)">
                                        <div class="h-1.5 bg-mw-amber/20 rounded-full overflow-hidden">
                                            <div class="h-full bg-mw-amber rounded-full" :style="'width: ' + progress + '%'"></div>
                                        </div>
                                        <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 bg-white rounded-full shadow-lg border-2 border-mw-amber"
                                             :style="'left: calc(' + progress + '% - 8px)'"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 w-10 tabular-nums" x-text="formatTime(duration)">0:00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Notice --}}
            <p class="text-center text-gray-600 text-xs mt-6">
                Contenu confidentiel — Écoute réservée à {{ $proAccount->structure }}
            </p>
        </div>
    </div>
</section>

<script>
function proPlayer() {
    return {
        audio: null,
        currentTrack: null,
        isPlaying: false,
        isLoading: false,
        currentTime: 0,
        duration: 0,
        progress: 0,
        dragging: false,
        dragBar: null,

        async togglePlay(trackId, infoUrl) {
            if (this.currentTrack === trackId && this.audio) {
                if (this.isPlaying) {
                    this.audio.pause();
                    this.isPlaying = false;
                } else {
                    this.audio.play();
                    this.isPlaying = true;
                }
                return;
            }

            this.cleanup();
            this.currentTrack = trackId;
            this.isLoading = true;
            this.progress = 0;
            this.currentTime = 0;

            try {
                // 1. Fetch track info + chunk URLs
                const h = { 'X-Audio-Stream': 'mw' };
                const infoRes = await fetch(infoUrl, { credentials: 'same-origin', headers: h });
                if (!infoRes.ok) throw new Error('Erreur info');
                const info = await infoRes.json();

                // 2. Fetch first chunk → start playback immediately
                const firstRes = await fetch(info.chunks[0], { credentials: 'same-origin', headers: h });
                if (!firstRes.ok) throw new Error('Chunk 0 failed');
                const firstChunk = await firstRes.arrayBuffer();

                const partialBlob = new Blob([firstChunk], { type: info.mime });
                this.audio = new Audio();
                this.audio.src = URL.createObjectURL(partialBlob);
                this.setupAudioEvents();

                await this.audio.play();
                this.isPlaying = true;
                this.isLoading = false;

                // 3. Load remaining chunks in background → swap to complete blob
                if (info.chunks.length > 1) {
                    const allParts = [firstChunk];
                    for (let i = 1; i < info.chunks.length; i++) {
                        const r = await fetch(info.chunks[i], { credentials: 'same-origin', headers: h });
                        if (!r.ok) throw new Error('Chunk ' + i + ' failed');
                        allParts.push(await r.arrayBuffer());
                    }

                    const fullBlob = new Blob(allParts, { type: info.mime });
                    const fullUrl = URL.createObjectURL(fullBlob);
                    const currentTime = this.audio.currentTime;
                    const wasPlaying = !this.audio.paused;

                    URL.revokeObjectURL(this.audio.src);
                    this.audio.src = fullUrl;
                    this.audio.currentTime = currentTime;
                    if (wasPlaying) this.audio.play();
                }
            } catch (e) {
                console.error('Player error:', e);
                this.isLoading = false;
                this.currentTrack = null;
            }
        },

        setupAudioEvents() {
            this.audio.addEventListener('loadedmetadata', () => {
                this.duration = this.audio.duration;
            });
            this.audio.addEventListener('timeupdate', () => {
                if (this.dragging) return;
                this.currentTime = this.audio.currentTime;
                this.duration = this.audio.duration || 0;
                this.progress = this.duration > 0 ? (this.currentTime / this.duration) * 100 : 0;
            });
            this.audio.addEventListener('ended', () => {
                this.isPlaying = false;
                this.progress = 100;
            });
        },

        cleanup() {
            if (this.audio) {
                this.audio.pause();
                if (this.audio.src) URL.revokeObjectURL(this.audio.src);
                this.audio = null;
            }
        },

        seek(event) {
            if (!this.audio || !this.duration) return;
            const rect = event.currentTarget.getBoundingClientRect();
            const percent = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
            this.audio.currentTime = percent * this.duration;
            this.currentTime = this.audio.currentTime;
            this.progress = percent * 100;
        },

        startDrag(event) {
            if (!this.audio || !this.duration) return;
            this.dragging = true;
            this.dragBar = event.currentTarget;
            this.seek(event);
        },

        onDrag(event) {
            if (!this.dragging || !this.dragBar) return;
            const rect = this.dragBar.getBoundingClientRect();
            const percent = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
            this.audio.currentTime = percent * this.duration;
            this.currentTime = this.audio.currentTime;
            this.progress = percent * 100;
        },

        stopDrag() {
            this.dragging = false;
            this.dragBar = null;
        },

        formatTime(seconds) {
            if (!seconds || isNaN(seconds)) return '0:00';
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60);
            return m + ':' + (s < 10 ? '0' : '') + s;
        }
    };
}
</script>
@endsection
