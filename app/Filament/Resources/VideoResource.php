<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = 'Médias';

    protected static ?string $modelLabel = 'Vidéo';

    protected static ?string $pluralModelLabel = 'Vidéos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vidéo')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('youtube_url')
                            ->label('URL YouTube')
                            ->required()
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...'),
                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options([
                                'clip' => 'Clip officiel',
                                'live' => 'Live',
                                'session' => 'Session',
                                'interview' => 'Interview',
                                'other' => 'Autre',
                            ])
                            ->default('clip')
                            ->required(),
                        Forms\Components\DatePicker::make('published_at')
                            ->label('Date de publication')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publié')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'clip' => 'Clip officiel',
                        'live' => 'Live',
                        'session' => 'Session',
                        'interview' => 'Interview',
                        'other' => 'Autre',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'clip' => 'danger',
                        'live' => 'success',
                        'session' => 'warning',
                        'interview' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options([
                        'clip' => 'Clip officiel',
                        'live' => 'Live',
                        'session' => 'Session',
                        'interview' => 'Interview',
                        'other' => 'Autre',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publié'),
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }
}
