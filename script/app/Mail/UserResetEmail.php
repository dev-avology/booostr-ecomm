<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(public $data)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tenantId = function_exists('tenant') && tenant() ? (tenant('id') ?: (tenant()->id ?? 'club')) : 'club';
        $fromEmail = $tenantId . '+pwhelp@mktg.booostr.co';
        $fromName = 'Password Reset Request';
        $siteHost = parse_url(url('/'), PHP_URL_HOST) ?: url('/');
        $subject = 'Password Reset Request for ' . $siteHost;

        $mailable = $this->from($fromEmail, $fromName)
            ->subject($subject)
            ->view('emails.user-reset-password')
            ->with([
                'data' => $this->data,
            ]);

        if (!empty($this->data['support_email']) && filter_var($this->data['support_email'], FILTER_VALIDATE_EMAIL)) {
            $mailable->replyTo($this->data['support_email']);
        }

        return $mailable;
    }
}
