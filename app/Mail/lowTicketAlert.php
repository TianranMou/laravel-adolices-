<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowTicketAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $availableTicketsCount;
    public $minTicketThreshold;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($availableTicketsCount, $minTicketThreshold)
    {
        $this->availableTicketsCount = $availableTicketsCount;
        $this->minTicketThreshold = $minTicketThreshold;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Low Ticket Alert',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.low_ticket_alert', // Create this view file
            with: [
                'availableTicketsCount' => $this->availableTicketsCount,
                'minTicketThreshold' => $this->minTicketThreshold,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
