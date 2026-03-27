<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Le Groupe';

    protected static ?string $modelLabel = 'Membre';

    protected static ?string $pluralModelLabel = 'Membres';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom / Pseudo')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('instruments')
                            ->label('Instrument(s)')
                            ->required()
                            ->placeholder('Ex: Guitare, Chant')
                            ->maxLength(255),
                        Forms\Components\Placeholder::make('user_name')
                            ->label('Compte utilisateur lié')
                            ->content(fn (?Member $record) => $record?->user?->name ?? 'Aucun')
                            ->visible(fn (string $operation) => $operation === 'edit'),
                    ])->columns(2),

                Forms\Components\Section::make('Photo')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Photo du membre')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('3:4')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('800')
                            ->directory('members')
                            ->visibility('public'),
                    ]),

                Forms\Components\Section::make('Biographie')
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                            ->label('Bio courte')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Réseaux sociaux')
                    ->description('Liens vers les profils personnels du membre (optionnel)')
                    ->schema([
                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/...'),
                        Forms\Components\TextInput::make('facebook')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/...'),
                        Forms\Components\TextInput::make('twitter')
                            ->label('X (Twitter)')
                            ->url()
                            ->placeholder('https://x.com/...'),
                        Forms\Components\TextInput::make('youtube')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://youtube.com/...'),
                        Forms\Components\TextInput::make('website')
                            ->label('Site web')
                            ->url()
                            ->placeholder('https://...'),
                    ])->columns(2)
                    ->collapsed(),

                Forms\Components\Hidden::make('sort_order')
                    ->default(fn () => Member::max('sort_order') + 1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=1f2937&color=fff'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom / Pseudo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('instruments')
                    ->label('Instrument(s)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Compte lié')
                    ->placeholder('Non lié')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->filters([])
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }
}
