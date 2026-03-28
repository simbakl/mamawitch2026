<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConcertResource\Pages;
use App\Models\Concert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConcertResource extends Resource
{
    protected static ?string $model = Concert::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Concert';

    protected static ?string $pluralModelLabel = 'Concerts';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre de l\'événement')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                $date = $get('date');
                                $dateStr = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');
                                $set('slug', \Illuminate\Support\Str::slug($state . '-' . $dateStr));
                            }),
                        Forms\Components\Hidden::make('slug'),
                        Forms\Components\DateTimePicker::make('date')
                            ->label('Date et heure')
                            ->required()
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->native(false),
                        Forms\Components\TextInput::make('venue')
                            ->label('Lieu / Salle')
                            ->required()
                            ->placeholder('Ex: Le Backstage')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Adresse')
                            ->placeholder('Ex: 12 rue de la Roquette')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Code postal')
                            ->placeholder('Ex: 75011')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('city')
                            ->label('Ville')
                            ->required()
                            ->placeholder('Ex: Paris')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Billetterie & Statut')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_url')
                            ->label('Lien billetterie')
                            ->url()
                            ->placeholder('https://...')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'upcoming' => 'À venir',
                                'soldout' => 'Complet',
                                'cancelled' => 'Annulé',
                            ])
                            ->default('upcoming')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label('Type (interne)')
                            ->options([
                                'concert' => 'Concert',
                                'festival' => 'Festival',
                                'release_party' => 'Release Party',
                                'showcase' => 'Showcase',
                                'other' => 'Autre',
                            ])
                            ->default('concert')
                            ->required()
                            ->helperText('Visible uniquement dans l\'admin'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publié sur le site')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Détails')
                    ->schema([
                        Forms\Components\FileUpload::make('poster')
                            ->label('Affiche')
                            ->image()
                            ->directory('concerts')
                            ->visibility('public')
                            ->imageResizeTargetWidth('800'),
                        Forms\Components\RichEditor::make('description')
                            ->label('Description / Notes')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline',
                                'h2', 'h3',
                                'bulletList', 'orderedList',
                                'link', 'blockquote',
                                'redo', 'undo',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('poster')
                    ->label('')
                    ->square()
                    ->size(40),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Événement')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('venue')
                    ->label('Lieu')
                    ->searchable()
                    ->description(fn (Concert $record) => $record->city),
                Tables\Columns\TextColumn::make('display_status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'À venir' => 'success',
                        'Complet' => 'warning',
                        'Annulé' => 'danger',
                        'Passé' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'concert' => 'Concert',
                        'festival' => 'Festival',
                        'release_party' => 'Release Party',
                        'showcase' => 'Showcase',
                        'other' => 'Autre',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'upcoming' => 'À venir',
                        'soldout' => 'Complet',
                        'cancelled' => 'Annulé',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'concert' => 'Concert',
                        'festival' => 'Festival',
                        'release_party' => 'Release Party',
                        'showcase' => 'Showcase',
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
            'index' => Pages\ListConcerts::route('/'),
            'create' => Pages\CreateConcert::route('/create'),
            'edit' => Pages\EditConcert::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }
}
