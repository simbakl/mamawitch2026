<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MusicProjectResource\Pages;
use App\Models\MusicProject;
use App\Models\MusicTrack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class MusicProjectResource extends Resource
{
    protected static ?string $model = MusicProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $navigationGroup = 'Espace Pro';

    protected static ?string $modelLabel = 'Projet musical';

    protected static ?string $pluralModelLabel = 'Projets musicaux';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Projet')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex : EP2 - Work in progress'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Pistes')
                    ->schema([
                        Forms\Components\Repeater::make('tracks')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->maxLength(255),

                                // Player for existing file
                                Forms\Components\Placeholder::make('audio_player')
                                    ->label('Fichier actuel')
                                    ->visible(fn (Forms\Get $get) => filled($get('file_path')) && filled($get('id')))
                                    ->content(function (Forms\Get $get) {
                                        $track = MusicTrack::find($get('id'));
                                        if (! $track) {
                                            return '';
                                        }
                                        $fileName = $track->file_name ?? basename($track->file_path);
                                        $disk = Storage::disk('pro-audio');
                                        $size = $disk->exists($track->file_path) ? round($disk->size($track->file_path) / 1048576, 1) : 0;
                                        $url = route('admin.audio.stream', $track);

                                        return new HtmlString('
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-3 text-sm">
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-green-500/10 text-green-400 rounded text-xs font-medium">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                                        ' . e($fileName) . '
                                                    </span>
                                                    <span class="text-gray-500">' . $size . ' Mo</span>
                                                </div>
                                                <audio controls preload="none" class="w-full h-10" style="border-radius: 8px;">
                                                    <source src="' . e($url) . '" type="audio/mpeg">
                                                </audio>
                                            </div>
                                        ');
                                    }),

                                // Hidden field to track existing record ID
                                Forms\Components\Hidden::make('id'),

                                // Upload: new file or replacement
                                Forms\Components\FileUpload::make('file_path')
                                    ->label(fn (Forms\Get $get) => filled($get('file_path')) && filled($get('id')) ? 'Remplacer le fichier' : 'Fichier audio')
                                    ->disk('pro-audio')
                                    ->directory('projects')
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/mp3'])
                                    ->maxSize(51200)
                                    ->storeFileNamesIn('file_name')
                                    ->required(fn (string $operation) => $operation === 'create')
                                    ->helperText(fn (Forms\Get $get) => filled($get('file_path')) && filled($get('id')) ? 'Uploadez un nouveau fichier pour remplacer l\'actuel.' : null),

                                Forms\Components\TextInput::make('duration')
                                    ->label('Durée')
                                    ->placeholder('3:42'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nouvelle piste')
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->orderColumn('sort_order')
                            ->collapsible()
                            ->collapsed()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une piste'),
                    ]),

                Forms\Components\Section::make('Accès')
                    ->schema([
                        Forms\Components\CheckboxList::make('proAccounts')
                            ->label('Pros autorisés')
                            ->relationship('proAccounts', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name} ({$record->structure})")
                            ->columns(2)
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tracks_count')
                    ->label('Pistes')
                    ->counts('tracks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pro_accounts_count')
                    ->label('Pros autorisés')
                    ->counts('proAccounts')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMusicProjects::route('/'),
            'create' => Pages\CreateMusicProject::route('/create'),
            'edit' => Pages\EditMusicProject::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
