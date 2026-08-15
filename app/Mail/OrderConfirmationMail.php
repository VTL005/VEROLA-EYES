<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Order cần gửi trong email.
     */
    public function __construct(
        public Order $order
    ) {
    }


    /**
     * Tiêu đề email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject:
                'VELORA Eyes - Xác nhận đơn hàng '
                . $this->order->order_code
        );
    }


    /**
     * View nội dung email.
     */
    public function content(): Content
    {
        return new Content(
            view:
                'emails.orders.confirmation'
        );
    }


    /**
     * File đính kèm.
     */
    public function attachments(): array
    {
        return [];
    }
}