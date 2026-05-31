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

    /**
     * @param  string  $mode  invoice | dp-verified | settlement-received | settlement-verified | reminder
     */
    public function __construct(public array $order, public string $mode = 'invoice') {}

    public function envelope(): Envelope
    {
        $subject = match ($this->mode) {
            'dp-verified' => 'DP Diterima — Pelunasan Pesanan '.$this->order['id'],
            'settlement-received' => 'Bukti Pelunasan Diterima — Pesanan '.$this->order['id'],
            'settlement-verified' => 'Pembayaran Lunas — Pesanan '.$this->order['id'],
            'reminder' => 'Pengingat Pelunasan — Pesanan '.$this->order['id'],
            default => 'Invoice Pesanan '.$this->order['id'],
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
            with: ['order' => $this->order, 'mode' => $this->mode],
        );
    }
}
