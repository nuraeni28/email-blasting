<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BlastEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $messageText;

    /**
     * Create a new message instance.
     *
     * @param string $messageText
     */
    public function __construct($messageText)
    {
        $this->messageText = $messageText;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Blast Email')
                    ->view('email.blast')
                    ->with('messageData', $this->messageText);
    }
}
