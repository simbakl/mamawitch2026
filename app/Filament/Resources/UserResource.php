<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Rôles')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Rôles attribués')
                            ->relationship('roles', 'name')
                            ->options(
                                Role::whereIn('name', ['admin', 'editor', 'musician'])
                                    ->pluck('name', 'id')
                                    ->map(fn ($name) => match ($name) {
                                        'admin' => 'Administrateur',
                                        'editor' => 'Éditeur',
                                        'musician' => 'Musicien',
                                        default => $name,
                                    })
                            )
                            ->descriptions([
                                'admin' => 'Accès total à toutes les fonctionnalités',
                                'editor' => 'Gestion du contenu public (concerts, news, galeries, vidéos, discographie)',
                                'musician' => 'Gestion de sa fiche membre, matériel et besoins techniques',
                            ])
                            ->columns(3),
                    ]),

                Forms\Components\Section::make('Google SSO')
                    ->schema([
                        Forms\Components\Placeholder::make('google_status')
                            ->label('Compte Google')
                            ->content(fn (?User $record) => $record?->google_id
                                ? 'Lié'
                                : 'Non lié'
                            ),
                        Forms\Components\Toggle::make('remove_google')
                            ->label('Délier le compte Google')
                            ->visible(fn (?User $record) => (bool) $record?->google_id)
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, ?User $record) {
                                if ($state && $record) {
                                    $record->update(['google_id' => null, 'avatar' => null]);
                                }
                            }),
                    ])
                    ->visible(fn (string $operation) => $operation === 'edit')
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=1f2937&color=fff')
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôles')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'admin' => 'Admin',
                        'editor' => 'Éditeur',
                        'musician' => 'Musicien',
                        'pro' => 'Pro',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'admin' => 'danger',
                        'editor' => 'warning',
                        'musician' => 'success',
                        'pro' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('google_id')
                    ->label('Google')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rôle')
                    ->relationship('roles', 'name')
                    ->options([
                        'admin' => 'Admin',
                        'editor' => 'Éditeur',
                        'musician' => 'Musicien',
                        'pro' => 'Pro',
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
