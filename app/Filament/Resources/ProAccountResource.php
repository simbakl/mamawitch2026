<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProAccountResource\Pages;
use App\Mail\ProAccessApprovedMail;
use App\Mail\ProInvitationMail;
use App\Models\MusicProject;
use App\Models\ProAccount;
use App\Models\ProType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProAccountResource extends Resource
{
    protected static ?string $model = ProAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Espace Pro';

    protected static ?string $modelLabel = 'Compte pro';

    protected static ?string $pluralModelLabel = 'Comptes pro';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('proType');
    }

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = cache()->remember('nav_badge_pending_pro', 60, function () {
            return ProAccount::pending()->count();
        });

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('structure')
                            ->label('Structure / Média')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('pro_type_id')
                            ->label('Type')
                            ->relationship('proType', 'name')
                            ->required(),
                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->rows(3)
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Statut & Accès')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'approved' => 'Approuvé',
                                'rejected' => 'Refusé',
                                'invited' => 'Invité',
                                'disabled' => 'Désactivé',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Placeholder::make('google_linked')
                            ->label('Compte Google')
                            ->content(fn (?ProAccount $record) => $record?->user_id ? 'Lié' : 'Non lié'),
                    ])->columns(2),

                Forms\Components\Section::make('Écoute privée')
                    ->schema([
                        Forms\Components\CheckboxList::make('musicProjects')
                            ->label('Projets musicaux accessibles')
                            ->relationship('musicProjects', 'title')
                            ->descriptions(
                                MusicProject::pluck('description', 'id')->toArray()
                            )
                            ->columns(2),
                    ])
                    ->visible(fn () => MusicProject::count() > 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nom')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('structure')
                    ->label('Structure')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proType.name')
                    ->label('Type')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Refusé',
                        'invited' => 'Invité',
                        'disabled' => 'Désactivé',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'invited' => 'info',
                        'disabled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('user_id')
                    ->label('Google')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn (ProAccount $record) => $record->user_id !== null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Refusé',
                        'invited' => 'Invité',
                        'disabled' => 'Désactivé',
                    ]),
                Tables\Filters\SelectFilter::make('pro_type_id')
                    ->label('Type')
                    ->relationship('proType', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ProAccount $record) => $record->status === 'pending')
                    ->action(function (ProAccount $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);
                        Mail::to($record->email)->send(new ProAccessApprovedMail($record));
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ProAccount $record) => $record->status === 'pending')
                    ->action(function (ProAccount $record) {
                        $record->update(['status' => 'rejected']);
                    }),
                Tables\Actions\Action::make('generate_invitation')
                    ->label('Générer invitation')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->action(function (ProAccount $record) {
                        $token = Str::random(64);
                        $record->update([
                            'invitation_token' => $token,
                            'status' => 'invited',
                            'invitation_sent_at' => now(),
                        ]);
                        Mail::to($record->email)->send(new ProInvitationMail($record));
                    })
                    ->visible(fn (ProAccount $record) => in_array($record->status, ['pending', 'approved'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProAccounts::route('/'),
            'create' => Pages\CreateProAccount::route('/create'),
            'edit' => Pages\EditProAccount::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
