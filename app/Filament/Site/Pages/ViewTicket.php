<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Filament\Notifications\Notification;

class ViewTicket extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationLabel = 'View Ticket';
    protected static ?string $title = 'View Ticket';
    protected static ?string $slug = 'view-ticket'; // ✅ آدرس صفحه

    // 🚫 این دو مورد مهم هستن که از منو حذف بشه
    protected static bool $shouldRegisterNavigation = false;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static string $view = 'filament.site.pages.view-ticket';

    public ?Ticket $ticket = null;
    public string $reply = '';

    public function mount(): void
    {
        $record = request()->get('record'); // پارامتر از ?record=8
        abort_if(!$record, 404);

        $this->ticket = Ticket::where('id', $record)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function sendReply(): void
    {
        if (blank($this->reply)) {
            Notification::make()
                ->title('Please enter a message before sending.')
                ->danger()
                ->send();
            return;
        }

        TicketMessage::create([
            'ticket_id' => $this->ticket->id,
            'sender_id' => auth()->id(),
            'message' => $this->reply,
        ]);

        $this->reply = '';

        Notification::make()
            ->title('Reply sent successfully!')
            ->success()
            ->send();
    }

    public function getMessagesProperty()
    {
        return $this->ticket
            ->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();
    }
}
