<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @param array $mailData
     */
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->mailData['subject'] ?? 'Welcome to your account';
        $fromEmail = !empty($this->mailData['from_email']) ? $this->mailData['from_email'] : config('mail.from.address');
        $fromName = !empty($this->mailData['club_name']) ? $this->mailData['club_name'] : config('mail.from.name');

        $mailable = $this->subject($subject)
            ->view('mail.customer_account_created')
            ->with([
                'mailData' => $this->mailData,
            ]);

        if (!empty($fromEmail)) {
            $mailable->from($fromEmail, $fromName);
        }

        if (!empty($this->mailData['support_email']) && filter_var($this->mailData['support_email'], FILTER_VALIDATE_EMAIL)) {
            $mailable->replyTo($this->mailData['support_email'], $fromName);
        }

        return $mailable;
    }
}

