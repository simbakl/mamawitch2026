<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Actualité';

    protected static ?string $pluralModelLabel = 'Actualités';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('category');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenu')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\Hidden::make('slug'),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Résumé / Texte court')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Affiché dans le listing des actualités')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('body')
                            ->label('Contenu détaillé (optionnel)')
                            ->helperText('Si rempli, un bouton "Lire la suite" apparaîtra vers une page dédiée')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3',
                                'bulletList', 'orderedList',
                                'link', 'blockquote',
                                'redo', 'undo',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Médias')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Image à la une')
                            ->image()
                            ->directory('news')
                            ->visibility('public')
                            ->imageResizeTargetWidth('1200')
                            ->maxSize(5120),
                        Forms\Components\TextInput::make('youtube_url')
                            ->label('Vidéo YouTube')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('URL de la vidéo YouTube à intégrer'),
                    ])->columns(2),

                Forms\Components\Section::make('Publication')
                    ->schema([
                        Forms\Components\Select::make('news_category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                Forms\Components\Hidden::make('slug'),
                            ]),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Date de publication')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->native(false)
                            ->helperText('Laisser vide = publication immédiate. Date future = publication programmée.'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publié')
                            ->default(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('')
                    ->square()
                    ->size(50),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publication')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn (News $record) => match (true) {
                        ! $record->is_published => 'Brouillon',
                        $record->isScheduled() => 'Programmé',
                        $record->isVisible() => 'En ligne',
                        default => '',
                    })
                    ->color(fn (News $record) => match (true) {
                        ! $record->is_published => 'gray',
                        $record->isScheduled() => 'warning',
                        $record->isVisible() => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('body')
                    ->label('Détail')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->getStateUsing(fn (News $record) => $record->hasDetailPage()),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('news_category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publié'),
                Tables\Filters\Filter::make('scheduled')
                    ->label('Programmé')
                    ->query(fn ($query) => $query->where('is_published', true)->where('published_at', '>', now())),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }
}
