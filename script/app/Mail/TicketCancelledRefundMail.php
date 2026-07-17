<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCancelledRefundMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $mailable = $this->subject($this->data['subject'])
            ->view('mail.ticket-cancelled-refund')
            ->with($this->data);

        if (function_exists('apply_store_receipt_mail_identity')) {
            apply_store_receipt_mail_identity($mailable);
        }

        return $mailable;
    }
}
