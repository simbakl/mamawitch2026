<x-filament-panels::page>

    {{-- STAGE PLAN EDITOR --}}
    <div class="mb-8" wire:ignore>
        <h2 class="text-xl font-bold mb-4 text-gray-200">Plan de scène</h2>

        <div x-data="stagePlanEditor(@js($this->stagePlanElements), @js($this->stagePlanWidth), @js($this->stagePlanDepth))"
             @mousemove.window="onDrag($event)"
             @mouseup.window="stopDragOrResize()"
             @touchmove.window.prevent="onDrag($event)"
             @touchend.window="stopDragOrResize()">

            {{-- Stage dimensions --}}
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="inline-flex items-center gap-x-3 mb-2"><span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Preset</span></label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select @change="applyPreset($event.target.value)">
                            <option value="">Personnalisé</option>
                            <option value="600,400">Petite salle (6m x 4m)</option>
                            <option value="800,500">Salle moyenne (8m x 5m)</option>
                            <option value="1000,600">Grande salle (10m x 6m)</option>
                            <option value="1200,800">Très grande salle (12m x 8m)</option>
                            <option value="1500,1000">Festival (15m x 10m)</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="inline-flex items-center gap-x-3 mb-2"><span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Largeur (cm)</span></label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" x-model.number="stageWidth" step="50" min="200" max="2000" class="text-center" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="inline-flex items-center gap-x-3 mb-2"><span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Profondeur (cm)</span></label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" x-model.number="stageDepth" step="50" min="200" max="2000" class="text-center" />
                    </x-filament::input.wrapper>
                </div>
                <div class="flex items-end pb-2">
                    <span class="text-gray-400 text-sm">
                        = <span x-text="(stageWidth/100).toFixed(1)"></span>m x <span x-text="(stageDepth/100).toFixed(1)"></span>m
                    </span>
                </div>
            </div>

            <div class="flex gap-6">
                {{-- TOOLBAR --}}
                <div class="w-44 shrink-0 space-y-1.5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Ajouter un élément</p>
                    <template x-for="type in elementTypes" :key="type.key">
                        <button @click="addElement(type.key)"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-left transition-colors cursor-pointer ring-1 ring-inset ring-white/10 hover:ring-white/20 bg-white/5 hover:bg-white/10">
                            <span class="w-6 h-6 rounded flex items-center justify-center text-sm"
                                :style="'background:' + type.color + '33; color:' + type.color"
                                x-text="type.icon"></span>
                            <span class="text-xs text-gray-300" x-text="type.label"></span>
                        </button>
                    </template>
                </div>

                {{-- STAGE CANVAS --}}
                <div class="flex-1 min-w-0">
                    <div class="relative bg-gray-900 border-2 border-gray-600 rounded-lg select-none w-full"
                         x-ref="stageCanvas"
                         :style="'aspect-ratio: ' + stageWidth + '/' + stageDepth + '; min-height: 300px;'"
                         style="touch-action: none;"
                         @click.self="deselectAll()">

                        {{-- Grid --}}
                        <div class="absolute inset-0 opacity-10 rounded-lg pointer-events-none" style="background-image: linear-gradient(rgba(255,255,255,.3) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.3) 1px, transparent 1px); background-size: 10% 10%;"></div>

                        {{-- Fond de scène (top) --}}
                        <div class="absolute top-1 w-full text-center text-[10px] text-gray-600 uppercase tracking-widest pointer-events-none">
                            Fond de scène
                        </div>

                        {{-- Stage front edge --}}
                        <div class="absolute bottom-[8%] left-[5%] right-[5%] border-t-2 border-dashed border-gray-400 pointer-events-none"></div>

                        {{-- Public zone (bottom) --}}
                        <div class="absolute bottom-0 left-0 right-0 h-[8%] bg-white/5 pointer-events-none text-center" style="display: table; width: 100%;">
                            <span class="text-[10px] text-gray-400 uppercase tracking-[0.2em]" style="display: table-cell; vertical-align: middle;">&#9660; Public &#9660;</span>
                        </div>

                        {{-- Elements --}}
                        <template x-for="el in elements" :key="el.id">
                            <div class="absolute rounded shadow-lg cursor-move border-2 transition-[border-color] overflow-hidden"
                                 :style="elementStyle(el)"
                                 :class="selectedId === el.id ? 'border-amber-400 !z-50' : 'border-transparent z-10'"
                                 @mousedown.stop.prevent="startDrag($event, el.id)"
                                 @touchstart.stop.prevent="startDrag($event, el.id)"
                                 @click.stop="selectElement(el.id)">
                                <div class="w-full h-full flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-sm leading-none" x-text="getType(el.type).icon"></span>
                                    <span class="text-[7px] text-white/90 leading-tight text-center truncate w-full px-0.5 mt-0.5" x-text="el.label"></span>
                                </div>
                                {{-- Resize handle --}}
                                <div x-show="selectedId === el.id"
                                     class="absolute bottom-0 right-0 w-3 h-3 bg-amber-400 cursor-se-resize z-50"
                                     style="clip-path: polygon(100% 0%, 100% 100%, 0% 100%);"
                                     @mousedown.stop.prevent="startResize($event, el.id)"
                                     @touchstart.stop.prevent="startResize($event, el.id)">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Edit panel --}}
            <div x-show="selectedId" x-transition class="mt-4 p-4 fi-section rounded-xl bg-gray-50 dark:bg-white/5 ring-1 ring-gray-950/5 dark:ring-white/10">
                <template x-if="selectedElement()">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="inline-flex items-center gap-x-3 mb-2"><span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Label</span></label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="text" x-bind:value="selectedElement()?.label" x-on:input="updateLabel($event.target.value)" />
                            </x-filament::input.wrapper>
                        </div>
                        <div>
                            <label class="inline-flex items-center gap-x-3 mb-2"><span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Largeur %</span></label>
                            <x-filament::input.wrapper class="w-20">
                                <x-filament::input type="number" x-bind:value="selectedElement()?.width" step="1" min="2" max="50" x-on:input="updateSize('width', $event.target.value)" class="text-center" />
                            </x-filament::input.wrapper>
                        </div>
                        <div>
                            <label class="inline-flex items-center gap-x-3 mb-2"><span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Hauteur %</span></label>
                            <x-filament::input.wrapper class="w-20">
                                <x-filament::input type="number" x-bind:value="selectedElement()?.height" step="1" min="2" max="50" x-on:input="updateSize('height', $event.target.value)" class="text-center" />
                            </x-filament::input.wrapper>
                        </div>
                        <div class="flex gap-2 pb-0.5">
                            <x-filament::icon-button icon="heroicon-o-arrow-uturn-left" @click="rotateElement(-45)" label="Rotation -45°" />
                            <x-filament::icon-button icon="heroicon-o-arrow-uturn-right" @click="rotateElement(45)" label="Rotation +45°" />
                        </div>
                        <x-filament::button color="info" size="sm" @click="duplicateElement()">
                            Dupliquer
                        </x-filament::button>
                        <x-filament::button color="danger" size="sm" @click="deleteElement()">
                            Supprimer
                        </x-filament::button>
                    </div>
                </template>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-4 mt-4">
                <div :class="dirty ? 'opacity-100' : 'opacity-50'">
                    <x-filament::button color="success" @click="saveAll()" x-bind:disabled="!dirty">
                        Enregistrer le plan
                    </x-filament::button>
                </div>
                <span x-show="dirty" class="text-xs text-amber-400">Modifications non sauvegardées</span>
                <span x-show="!dirty && elements.length > 0" class="text-xs text-green-400">Sauvegardé</span>
                <span class="text-xs text-gray-500 ml-auto" x-text="elements.length + ' élément(s)'"></span>
            </div>
        </div>
    </div>

    {{-- GLOBAL REQUIREMENTS FORM --}}
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Enregistrer les besoins globaux
            </x-filament::button>
        </div>
    </form>

    {{-- MEMBER EQUIPMENT OVERVIEW --}}
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4 text-gray-200">Récapitulatif du matériel par membre</h2>

        @foreach ($this->getMembers() as $member)
            <div class="bg-white/5 rounded-xl p-4 mb-4">
                <h3 class="text-lg font-semibold text-white mb-1">{{ $member->name }} <span class="text-sm text-gray-400 font-normal">— {{ $member->instruments }}</span></h3>

                @if ($member->equipment->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                        @foreach ($member->equipment as $item)
                            <div class="flex items-center gap-2 text-sm text-gray-300">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($item->category) {
                                        'instrument' => 'bg-blue-500/20 text-blue-400',
                                        'amp' => 'bg-orange-500/20 text-orange-400',
                                        'effect' => 'bg-purple-500/20 text-purple-400',
                                        'accessory' => 'bg-gray-500/20 text-gray-400',
                                        default => 'bg-gray-500/20 text-gray-400',
                                    } }}">
                                    {{ $item->category_label }}
                                </span>
                                <span>{{ $item->name }}</span>
                                @if ($item->notes)
                                    <span class="text-gray-500 text-xs">— {{ $item->notes }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 mt-1">Aucun matériel renseigné</p>
                @endif

                @if ($member->techRequirement)
                    <div class="mt-3 pt-3 border-t border-white/10">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Besoins techniques</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 text-sm text-gray-300">
                            @if ($member->techRequirement->monitors)
                                <div><span class="text-gray-500">Retours :</span> {{ $member->techRequirement->monitors }}</div>
                            @endif
                            @if ($member->techRequirement->microphones)
                                <div><span class="text-gray-500">Micros/DI :</span> {{ $member->techRequirement->microphones }}</div>
                            @endif
                            @if ($member->techRequirement->power)
                                <div><span class="text-gray-500">Électricité :</span> {{ $member->techRequirement->power }}</div>
                            @endif
                            @if ($member->techRequirement->monitoring)
                                <div><span class="text-gray-500">Monitoring :</span> {{ $member->techRequirement->monitoring }}</div>
                            @endif
                            @if ($member->techRequirement->other)
                                <div><span class="text-gray-500">Divers :</span> {{ $member->techRequirement->other }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- GENERATE PDF --}}
    <div class="mt-6">
        <a href="{{ route('tech-sheet.pdf') }}" target="_blank">
            <x-filament::button color="success" icon="heroicon-o-document-arrow-down">
                Générer la Fiche Technique PDF
            </x-filament::button>
        </a>
    </div>

    @script
    <script>
    Alpine.data('stagePlanEditor', (initialElements = [], initialWidth = 800, initialDepth = 500) => ({
        elements: JSON.parse(JSON.stringify(initialElements)),
        stageWidth: initialWidth,
        stageDepth: initialDepth,
        selectedId: null,
        dragging: null,
        resizing: null,
        dirty: false,

        elementTypes: [
            { key: 'guitar_amp',    label: 'Ampli guitare',   icon: '🎸', color: '#ef4444', w: 7, h: 6 },
            { key: 'bass_amp',      label: 'Ampli basse',     icon: '🎸', color: '#f97316', w: 7, h: 6 },
            { key: 'drum_kit',      label: 'Batterie',        icon: '🥁', color: '#8b5cf6', w: 14, h: 14 },
            { key: 'keyboard',      label: 'Clavier',         icon: '🎹', color: '#3b82f6', w: 12, h: 5 },
            { key: 'monitor_wedge', label: 'Retour',          icon: '🔊', color: '#22c55e', w: 6, h: 4 },
            { key: 'mic_stand',     label: 'Pied micro',      icon: '🎤', color: '#a855f7', w: 4, h: 4 },
            { key: 'di_box',        label: 'Boîte de direct', icon: '📦', color: '#64748b', w: 4, h: 3 },
            { key: 'vocal_mic',     label: 'Micro chant',     icon: '🎙️', color: '#ec4899', w: 4, h: 4 },
            { key: 'power_strip',   label: 'Multiprise',      icon: '⚡', color: '#eab308', w: 7, h: 3 },
            { key: 'riser',         label: 'Praticable',      icon: '⬛', color: '#78716c', w: 18, h: 12 },
            { key: 'custom',        label: 'Personnalisé',    icon: '📌', color: '#6b7280', w: 7, h: 5 },
        ],

        getType(key) {
            return this.elementTypes.find(t => t.key === key) || this.elementTypes[this.elementTypes.length - 1];
        },

        applyPreset(value) {
            if (!value) return;
            const [w, d] = value.split(',').map(Number);
            this.stageWidth = w;
            this.stageDepth = d;
            this.dirty = true;
        },

        addElement(typeKey) {
            const type = this.getType(typeKey);
            this.elements.push({
                id: 'el_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
                type: typeKey,
                label: type.label,
                x: 50 - type.w / 2,
                y: 50 - type.h / 2,
                width: type.w,
                height: type.h,
                rotation: 0,
            });
            this.dirty = true;
        },

        elementStyle(el) {
            const type = this.getType(el.type);
            // Use aspect-ratio trick: height is relative to the canvas width via the aspect-ratio
            // But since the container uses aspect-ratio CSS, percentage heights work relative to the container height
            return `left:${el.x}%;top:${el.y}%;width:${el.width}%;height:${el.height}%;background:${type.color}cc;transform:rotate(${el.rotation || 0}deg);`;
        },

        selectElement(id) { this.selectedId = id; },
        selectedElement() { return this.elements.find(e => e.id === this.selectedId) || null; },
        deselectAll() { this.selectedId = null; },

        updateLabel(value) {
            const el = this.selectedElement();
            if (el) { el.label = value; this.dirty = true; }
        },

        updateSize(prop, value) {
            const el = this.selectedElement();
            if (el) { el[prop] = Math.max(2, Math.min(50, parseInt(value) || 2)); this.dirty = true; }
        },

        rotateElement(degrees) {
            const el = this.selectedElement();
            if (el) { el.rotation = ((el.rotation || 0) + degrees) % 360; this.dirty = true; }
        },

        duplicateElement() {
            const el = this.selectedElement();
            if (!el) return;
            const copy = JSON.parse(JSON.stringify(el));
            copy.id = 'el_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
            copy.x = Math.min(90, el.x + 3);
            copy.y = Math.min(90, el.y + 3);
            this.elements.push(copy);
            this.selectedId = copy.id;
            this.dirty = true;
        },

        deleteElement() {
            this.elements = this.elements.filter(e => e.id !== this.selectedId);
            this.selectedId = null;
            this.dirty = true;
        },

        // DRAG
        startDrag(event, id) {
            if (this.resizing) return;
            const stage = this.$refs.stageCanvas;
            const rect = stage.getBoundingClientRect();
            const el = this.elements.find(e => e.id === id);

            const clientX = event.touches ? event.touches[0].clientX : event.clientX;
            const clientY = event.touches ? event.touches[0].clientY : event.clientY;

            this.dragging = {
                id,
                offsetX: clientX - rect.left - (el.x / 100 * rect.width),
                offsetY: clientY - rect.top - (el.y / 100 * rect.height),
            };
            this.selectElement(id);
        },

        // RESIZE
        startResize(event, id) {
            const stage = this.$refs.stageCanvas;
            const rect = stage.getBoundingClientRect();
            const el = this.elements.find(e => e.id === id);

            const clientX = event.touches ? event.touches[0].clientX : event.clientX;
            const clientY = event.touches ? event.touches[0].clientY : event.clientY;

            this.resizing = {
                id,
                startX: clientX,
                startY: clientY,
                startW: el.width,
                startH: el.height,
                stageW: rect.width,
                stageH: rect.height,
            };
        },

        onDrag(event) {
            const clientX = event.touches ? event.touches[0].clientX : event.clientX;
            const clientY = event.touches ? event.touches[0].clientY : event.clientY;

            // Handle resize
            if (this.resizing) {
                const el = this.elements.find(e => e.id === this.resizing.id);
                if (!el) return;

                const dxPct = ((clientX - this.resizing.startX) / this.resizing.stageW) * 100;
                const dyPct = ((clientY - this.resizing.startY) / this.resizing.stageH) * 100;

                el.width = Math.max(3, Math.min(50, this.resizing.startW + dxPct));
                el.height = Math.max(3, Math.min(50, this.resizing.startH + dyPct));
                this.dirty = true;
                return;
            }

            // Handle drag
            if (!this.dragging) return;

            const stage = this.$refs.stageCanvas;
            const rect = stage.getBoundingClientRect();
            const el = this.elements.find(e => e.id === this.dragging.id);
            if (!el) return;

            let newX = ((clientX - rect.left - this.dragging.offsetX) / rect.width) * 100;
            let newY = ((clientY - rect.top - this.dragging.offsetY) / rect.height) * 100;

            el.x = Math.max(0, Math.min(100 - el.width, newX));
            el.y = Math.max(0, Math.min(100 - el.height, newY));
            this.dirty = true;
        },

        stopDragOrResize() {
            this.dragging = null;
            this.resizing = null;
        },

        async saveAll() {
            await this.$wire.saveStagePlan(
                JSON.parse(JSON.stringify(this.elements)),
                this.stageWidth,
                this.stageDepth
            );
            this.dirty = false;
        },
    }));
    </script>
    @endscript
</x-filament-panels::page>
