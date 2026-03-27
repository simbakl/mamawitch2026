<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class MyAccount extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Mon Espace';

    protected static ?string $navigationLabel = 'Mon Compte';

    protected static ?string $title = 'Mon Compte';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.my-account';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
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
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('Compte Google')
                    ->description('Liez votre compte Google pour vous connecter plus rapidement.')
                    ->schema([
                        Forms\Components\Placeholder::make('google_status')
                            ->label('')
                            ->content(function () {
                                $user = auth()->user();
                                if ($user->google_id) {
                                    return new HtmlString(
                                        '<div class="flex items-center gap-2 text-green-400">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Compte Google lié (' . e($user->email) . ')
                                        </div>'
                                    );
                                }
                                return new HtmlString(
                                    '<span class="text-gray-400">Aucun compte Google lié.</span>'
                                );
                            }),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('link_google')
                                ->label('Lier mon compte Google')
                                ->icon('heroicon-o-link')
                                ->url(url('/auth/google'))
                                ->visible(fn () => ! auth()->user()->google_id),
                            Forms\Components\Actions\Action::make('unlink_google')
                                ->label('Délier mon compte Google')
                                ->icon('heroicon-o-x-mark')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->visible(fn () => (bool) auth()->user()->google_id)
                                ->action(function () {
                                    auth()->user()->update(['google_id' => null, 'avatar' => null]);
                                    Notification::make()->success()->title('Compte Google délié')->send();
                                    $this->redirect(static::getUrl());
                                }),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        auth()->user()->update([
            'name' => $state['name'],
        ]);

        Notification::make()
            ->success()
            ->title('Compte mis à jour')
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor', 'musician']) ?? false;
    }
}
