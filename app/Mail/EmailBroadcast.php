<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class EmailBroadcast extends Mailable
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
        if ($this->details['eventYear'] == '2023') {
            if ($this->details['eventCategory'] == "AFV") {
                $subject = '17ᵗʰ Annual GPCA Forum - Registration & Badge Information';
            } else {
                $subject = '17ᵗʰ Annual GPCA Forum - Registration & Badge Information';
            }
        } else {
            $subject = '17ᵗʰ Annual GPCA Forum - Registration & Badge Information';
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
        if ($this->details['eventYear'] == '2023') {
            if ($this->details['eventCategory'] == "AF") {
                if($this->details['registrationStatus'] == "confirmed"){
                    return new Content(
                        markdown: 'emails.2023.af.confirm-email-broadcast',
                    );
                } else {
                    return new Content(
                        markdown: 'emails.2023.af.pending-email-broadcast',
                    );
                }
            } else if ($this->details['eventCategory'] == "AFV") {
                if($this->details['registrationStatus'] == "confirmed"){
                    return new Content(
                        markdown: 'emails.2023.visitor.confirm-email-broadcast',
                    );
                } else {
                    return new Content(
                        markdown: 'emails.2023.visitor.pending-email-broadcast',
                    );
                }
            } else {
                return new Content(
                    markdown: 'emails.email-broadcast',
                );
            }
        } else {
            return new Content(
                markdown: 'emails.email-broadcast',
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
