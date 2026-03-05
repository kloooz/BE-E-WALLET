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

class TopUpPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;
    public $snapToken;
    public $expiryTime;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Transaction $transaction, string $snapToken)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->snapToken = $snapToken;
        // Midtrans default expiry is usually 24 hours from creation date
        $this->expiryTime = $transaction->created_at->addHours(24)->format('j M Y, H:i T');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Complete Your Top Up - DANAKU',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.topup_pending',
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
