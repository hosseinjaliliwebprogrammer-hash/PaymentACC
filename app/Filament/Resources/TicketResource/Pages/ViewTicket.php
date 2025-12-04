<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\TicketMessage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    // فیلد پاسخ جدید
    public ?string $reply = '';

    /**
     * عنوان صفحه
     */
    public function getTitle(): string
    {
        return 'Ticket Conversation';
    }

    /**
     * فقط اطلاعات پایه تیکت را نمایش می‌دهیم (بدون تغییر)
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * رندر صفحه گفتگو
     */
    public function render(): View
    {
        $messages = $this->record
            ->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        return view('filament.resources.tickets.view', [
            'record' => $this->record,
            'messages' => $messages,
        ])->layout('filament-panels::components.layout.base');
    }

    /**
     * ارسال پاسخ جدید
     */
    public function sendReply(): void
    {
        if (blank($this->reply)) {
            Notification::make()
                ->title('Please write a message before sending.')
                ->danger()
                ->send();

            return;
        }

        TicketMessage::create([
            'ticket_id' => $this->record->id,
            'sender_id' => auth()->id(),
            'message' => $this->reply,
        ]);

        $this->reply = '';

        Notification::make()
            ->title('Reply sent successfully!')
            ->success()
            ->send();
    }
}
