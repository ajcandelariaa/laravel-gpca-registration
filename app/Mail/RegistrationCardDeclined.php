<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class RegistrationCardDeclined extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $sendInvoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details, $sendInvoice = true)
    {
        $this->details = $details;
        $this->sendInvoice = $sendInvoice;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        if ($this->details['eventCategory'] == "RCCA" || $this->details['eventCategory'] == "SCEA") {
            $subject = 'Declined payment for your entry submission on the '. $this->details['eventName'];
        } else {
            $subject = 'Your payment for the '. $this->details['eventName'].' registration has been declined';
        }
        
        return new Envelope(
            from: new Address('forumregistration@gpca.org.ae', 'GPCA Events Registration'),
            subject: $subject,
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
            markdown: 'emails.registration-card-declined',
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
