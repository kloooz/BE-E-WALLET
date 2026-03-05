<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromoVoucherMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;
    public $voucherCode;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Transaction $transaction, string $voucherCode)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->voucherCode = $voucherCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Promo Voucher 🎉 - DANAKU',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.promo_voucher',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
