<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\Release;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReleaseResource extends Resource
{
    protected static ?string $model = Release::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    protected static ?string $navigationGroup = 'Médias';

    protected static ?string $modelLabel = 'Release';

    protected static ?string $pluralModelLabel = 'Discographie';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\Hidden::make('slug'),
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'single' => 'Single',
                                'ep' => 'EP',
                                'album' => 'Album',
                            ])
                            ->default('ep')
                            ->required(),
                        Forms\Components\DatePicker::make('release_date')
                            ->label('Date de sortie')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publié')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Visuel')
                    ->schema([
                        Forms\Components\FileUpload::make('cover')
                            ->label('Cover / Pochette')
                            ->image()
                            ->directory('releases')
                            ->visibility('public')
                            ->imageResizeTargetWidth('800')
                            ->imageCropAspectRatio('1:1')
                            ->maxSize(5120),
                    ]),

                Forms\Components\Section::make('Tracklist')
                    ->schema([
                        Forms\Components\Repeater::make('tracks')
                            ->relationship()
                            ->schema([
                                Forms\Components\Placeholder::make('track_position')
                                    ->label('N°')
                                    ->content(fn (Forms\Get $get) => $get('track_number') ?? '—'),
                                Forms\Components\Hidden::make('track_number'),
                                Forms\Components\TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('duration')
                                    ->label('Durée')
                                    ->placeholder('4:32')
                                    ->maxLength(10),
                            ])
                            ->columns(3)
                            ->reorderable('track_number')
                            ->orderColumn('track_number')
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un titre')
                            ->itemLabel(fn (array $state): ?string => ($state['track_number'] ?? '?') . '. ' . ($state['title'] ?? 'Nouveau titre'))
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Description & Crédits')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Présentation (optionnel)')
                            ->rows(3),
                        Forms\Components\Textarea::make('credits')
                            ->label('Crédits')
                            ->rows(4)
                            ->placeholder("Studio : ...\nProducteur : ...\nMixage : ...\nMastering : ..."),
                    ])->columns(2),

                Forms\Components\Section::make('Player intégré')
                    ->schema([
                        Forms\Components\TextInput::make('player_embed_url')
                            ->label('URL du player (Spotify/Bandcamp embed)')
                            ->url()
                            ->placeholder('https://open.spotify.com/embed/album/...')
                            ->helperText('Optionnel — URL d\'intégration du player'),
                    ]),

                Forms\Components\Section::make('Liens plateformes')
                    ->schema([
                        Forms\Components\TextInput::make('spotify_url')
                            ->label('Spotify')
                            ->url()
                            ->placeholder('https://open.spotify.com/album/...'),
                        Forms\Components\TextInput::make('bandcamp_url')
                            ->label('Bandcamp')
                            ->url()
                            ->placeholder('https://mamawitch.bandcamp.com/album/...'),
                        Forms\Components\TextInput::make('apple_music_url')
                            ->label('Apple Music')
                            ->url()
                            ->placeholder('https://music.apple.com/...'),
                        Forms\Components\TextInput::make('deezer_url')
                            ->label('Deezer')
                            ->url()
                            ->placeholder('https://www.deezer.com/album/...'),
                        Forms\Components\TextInput::make('soundcloud_url')
                            ->label('SoundCloud')
                            ->url()
                            ->placeholder('https://soundcloud.com/mamawitch/...'),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('release_date', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('')
                    ->square()
                    ->size(50),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => strtoupper($state))
                    ->color(fn (string $state) => match ($state) {
                        'album' => 'danger',
                        'ep' => 'warning',
                        'single' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('release_date')
                    ->label('Sortie')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tracks_count')
                    ->label('Titres')
                    ->counts('tracks'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'single' => 'Single',
                        'ep' => 'EP',
                        'album' => 'Album',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReleases::route('/'),
            'create' => Pages\CreateRelease::route('/create'),
            'edit' => Pages\EditRelease::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }
}
