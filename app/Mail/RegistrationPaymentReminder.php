<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class RegistrationPaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = "Payment reminder for your registration on the " . $this->details['eventName'];
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
        if ($this->details['eventCategory'] == "AFS") {
            return new Content(
                markdown: 'emails.2023.spouse.registration-payment-reminder',
            );
        } else if ($this->details['eventCategory'] == "AFV") {
            return new Content(
                markdown: 'emails.2023.visitor.registration-payment-reminder',
            );
        } else if ($this->details['eventCategory'] == "RCCA") {
            return new Content(
                markdown: 'emails.2023.rcca.registration-payment-reminder',
            );
        } else {
            return new Content(
                markdown: 'emails.registration-payment-reminder',
            );
        }
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
