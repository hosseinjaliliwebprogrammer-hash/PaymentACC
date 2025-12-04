<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDeliveryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * سفارش مربوط به ارسال ایمیل
     */
    public Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Envelope settings (subject)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your account details are ready',
        );
    }

    /**
     * Email content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.delivery',
            with: [
                'order' => $this->order, // 👈 پاس دادن سفارش به ویو ایمیل
            ],
        );
    }

    /**
     * Attachments (we don't need any)
     */
    public function attachments(): array
    {
        return [];
    }
}
