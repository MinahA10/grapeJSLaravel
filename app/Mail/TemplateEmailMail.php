<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $mjmlContent;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectText, $mjmlContent)
    {
        $this->subjectText = $subjectText;
        $this->mjmlContent = $mjmlContent;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                ->html($this->mjmlContent);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Template Email Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
