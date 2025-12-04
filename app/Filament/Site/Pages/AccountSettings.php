<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class AccountSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Account Settings';
    protected static ?string $title = 'Account Settings';
    protected static ?string $slug = 'account-settings';

    // ✅ این باعث می‌شود در سایدبار پنل یوزر نمایش داده شود
    protected static bool $shouldRegisterNavigation = true;

    protected static string $view = 'filament.site.pages.account-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        // پر کردن فرم با اطلاعات فعلی یوزر
        $this->form->fill([
            'name'  => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->required(),

                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->minLength(8)
                    ->nullable(),

                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->same('password')
                    ->nullable(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        $data = $this->form->getState();

        // چک کردن پسورد فعلی
        if (! Hash::check($data['current_password'] ?? '', $user->password)) {
            Notification::make()
                ->title('Current password is incorrect.')
                ->danger()
                ->send();

            return;
        }

        // آپدیت name و email
        $user->name  = $data['name'];
        $user->email = $data['email'];

        // اگر پسورد جدید وارد شده بود، عوض کن
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // خالی کردن فیلدهای پسورد بعد از ذخیره
        $this->form->fill([
            'name'  => $user->name,
            'email' => $user->email,
            'current_password'       => null,
            'password'               => null,
            'password_confirmation'  => null,
        ]);

        Notification::make()
            ->title('Account updated successfully.')
            ->success()
            ->send();
    }
}
