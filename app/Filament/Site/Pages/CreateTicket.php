<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class CreateTicket extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationLabel = 'New Ticket';
    protected static ?string $title = 'Create Ticket';
    protected static ?string $slug = 'create-ticket';

    protected static string $view = 'filament.site.pages.create-ticket';

    public $subject;
    public $department;
    public $priority = 'normal';
    public $message;

    public function submit(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'department' => 'required|string',
            'priority' => 'required|string',
            'message' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $this->subject,
            'department' => $this->department,
            'priority' => $this->priority,
            'status' => 'open',
            'message' => $this->message,
        ]);

        Notification::make()
            ->title('Your ticket has been created successfully!')
            ->success()
            ->send();

        // ✅ اصلاح مسیر ریدایرکت برای کار با query string
        $this->redirect("/app/view-ticket?record={$ticket->id}");
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->label('Subject')
                    ->required(),

                Forms\Components\Select::make('department')
                    ->label('Department')
                    ->options([
                        'sales' => 'Sales',
                        'technical' => 'Technical',
                    ])
                    ->required(),

                Forms\Components\Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                    ])
                    ->default('normal')
                    ->required(),

                Forms\Components\RichEditor::make('message')
                    ->label('Message')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'link',
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
