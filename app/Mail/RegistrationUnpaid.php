<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class RegistrationUnpaid extends Mailable
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
        if ($this->details['eventYear'] == '2023') {
            if ($this->details['eventCategory'] == "RCCA") {
                $subject = 'Outstanding payment for your entry submission on the ' . $this->details['eventName'];
            } else if ($this->details['eventCategory'] == "AFS") {
                $subject = 'Your Exclusive Spouse Program Experience at the 17th Annual GPCA Forum';
            } else {
                $subject = 'Outstanding payment for your ' . $this->details['eventName'] . ' registration';
            }
        } else {
            $subject = 'Outstanding payment for your ' . $this->details['eventName'] . ' registration';
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
                return new Content(
                    markdown: 'emails.2023.af.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "AFS") {
                return new Content(
                    markdown: 'emails.2023.spouse.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "AFV") {
                return new Content(
                    markdown: 'emails.2023.visitor.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "ANC") {
                return new Content(
                    markdown: 'emails.2023.anc.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "IPAW") {
                return new Content(
                    markdown: 'emails.2023.ipaw.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "PC") {
                return new Content(
                    markdown: 'emails.2023.pc.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "RCC") {
                return new Content(
                    markdown: 'emails.2023.rcc.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "RCCA") {
                return new Content(
                    markdown: 'emails.2023.rcca.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "SCC") {
                return new Content(
                    markdown: 'emails.2023.scc.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "PSW") {
                return new Content(
                    markdown: 'emails.2023.psw.registration-unpaid',
                );
            } else if ($this->details['eventCategory'] == "DAW") {
                return new Content(
                    markdown: 'emails.2023.daw.registration-unpaid',
                );
            } else {
                return new Content(
                    markdown: 'emails.registration-unpaid',
                );
            }
        } else {
            return new Content(
                markdown: 'emails.registration-unpaid',
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
