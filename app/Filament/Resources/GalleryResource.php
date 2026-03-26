<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Médias';

    protected static ?string $modelLabel = 'Galerie';

    protected static ?string $pluralModelLabel = 'Galeries Photos';

    protected static ?int $navigationSort = 1;

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
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publié')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Ajout rapide de photos')
                    ->schema([
                        Forms\Components\FileUpload::make('bulk_photos')
                            ->label('Uploader plusieurs photos')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('galleries')
                            ->visibility('public')
                            ->imageResizeTargetWidth('1200')
                            ->helperText('Sélectionnez plusieurs photos en une fois. Elles seront ajoutées à la galerie.')
                            ->dehydrated(false),
                    ])
                    ,

                Forms\Components\Section::make('Photos de la galerie')
                    ->schema([
                        Forms\Components\Repeater::make('photos')
                            ->relationship()
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Photo')
                                    ->image()
                                    ->required()
                                    ->directory('galleries')
                                    ->visibility('public')
                                    ->imageResizeTargetWidth('1200'),
                                Forms\Components\TextInput::make('caption')
                                    ->label('Légende')
                                    ->default(fn () => 'Photo')
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Photo')
                            ->reorderable('sort_order')
                            ->orderColumn('sort_order')
                            ->collapsible()
                            ->collapsed()
                            ->cloneable()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une photo'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('photos.0.image')
                    ->label('')
                    ->square()
                    ->size(50)
                    ->getStateUsing(fn (Gallery $record) => $record->photos->first()?->image),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Photos')
                    ->counts('photos'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),
            ])
            ->filters([
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }
}
