<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BooostrAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $clubName = $this->data['club_name'] ?? null;
        $tenantId = function_exists('tenant') && tenant() ? (tenant('id') ?: (tenant()->id ?? null)) : null;
        $slug = !empty($clubName) ? \Illuminate\Support\Str::slug($clubName) : (!empty($tenantId) ? $tenantId : 'support');
        
        // Sender: {club-slug}+site@mktg.booostr.co
        $fromEmail = $slug . '+site@mktg.booostr.co';
        $fromName = $clubName ?? config('mail.from.name', 'Booostr');

        $mail = $this->subject($this->data['subject'])
            ->from($fromEmail, $fromName)
            ->view('emails.booostr-account-created')
            ->with([
                'data' => $this->data,
            ]);

        // If club support email exists, set as reply-to
        if (!empty($this->data['support_email']) && filter_var($this->data['support_email'], FILTER_VALIDATE_EMAIL)) {
            $mail->replyTo($this->data['support_email'], $fromName);
        }

        return $mail;
    }
}

