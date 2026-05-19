<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Pesanan '.$this->order['id'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
            with: ['order' => $this->order],
        );
    }
}
