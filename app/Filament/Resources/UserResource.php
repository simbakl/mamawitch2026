<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Mail\AccountSetupMail;
use App\Mail\PasswordResetRequestMail;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
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
                        Forms\Components\Placeholder::make('password_info')
                            ->label('')
                            ->content('Un email sera envoyé à l\'utilisateur pour qu\'il configure son mot de passe.')
                            ->visible(fn (string $operation) => $operation === 'create'),
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

                Forms\Components\Section::make('Sécurité')
                    ->schema([
                        Forms\Components\Placeholder::make('google_status')
                            ->label('Compte Google')
                            ->content(fn (?User $record) => $record?->google_id
                                ? 'Lié'
                                : 'Non lié'
                            ),
                        Forms\Components\Placeholder::make('password_status')
                            ->label('Mot de passe')
                            ->content(fn (?User $record) => $record?->password
                                ? 'Défini'
                                : 'Non défini (connexion Google uniquement)'
                            ),
                        Forms\Components\Placeholder::make('reset_status')
                            ->label('Réinitialisation')
                            ->content(fn (?User $record) => $record?->must_reset_password
                                ? 'Réinitialisation en attente'
                                : 'Aucune'
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
                Tables\Actions\Action::make('force_reset')
                    ->label('Forcer reset')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Forcer la réinitialisation du mot de passe')
                    ->modalDescription('L\'utilisateur devra reconfigurer son mot de passe. Un email lui sera envoyé.')
                    ->action(function (User $record) {
                        $record->update([
                            'password' => null,
                            'must_reset_password' => true,
                        ]);
                        $record->generateSetupToken();
                        Mail::to($record->email)->send(new PasswordResetRequestMail($record->fresh()));
                        Notification::make()->title('Email de réinitialisation envoyé')->success()->send();
                    }),
                Tables\Actions\Action::make('resend_setup')
                    ->label('Renvoyer invitation')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->visible(fn (User $record) => ! $record->password && ! $record->google_id)
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->generateSetupToken();
                        Mail::to($record->email)->send(new AccountSetupMail($record->fresh()));
                        Notification::make()->title('Email d\'invitation renvoyé')->success()->send();
                    }),
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
