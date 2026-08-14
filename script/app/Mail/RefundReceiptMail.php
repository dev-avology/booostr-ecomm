<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    public string $subjectLine;

    public function __construct(array $data, string $subjectLine)
    {
        $this->data = $data;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        $mailable = $this->subject($this->subjectLine)
            ->view('mail.seller.refundreceipt')
            ->with($this->data);

        if (!empty($this->data['from'])) {
            $mailable->from($this->data['from'], $this->data['from_name'] ?? null);
        } elseif (function_exists('apply_store_receipt_mail_identity')) {
            apply_store_receipt_mail_identity($mailable);
        }

        if (!empty($this->data['reply_to'])) {
            $mailable->replyTo($this->data['reply_to'], $this->data['from_name'] ?? null);
        } elseif (function_exists('store_receipt_mail_from')) {
            $receiptFrom = store_receipt_mail_from();
            if (!empty($receiptFrom['reply_to'])) {
                $mailable->replyTo($receiptFrom['reply_to'], $receiptFrom['name'] ?? null);
            }
        }

        return $mailable;
    }
}
