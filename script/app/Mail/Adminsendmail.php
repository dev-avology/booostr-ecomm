<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Adminsendmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $subject;
    public $mailType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $subject, $mailType = null)
    {
        $this->data = $data;
        $this->subject = $subject;
        $this->mailType = $mailType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->data;
        $subject = $this->subject;
        $mailType = $this->mailType;

        $alertIdentity = function_exists('store_admin_alert_mail_from') ? store_admin_alert_mail_from() : [];
        $fromAddress = $alertIdentity['address'] ?? 'noreply+store-alert@mktg.booostr.co';
        $fromName = $alertIdentity['name'] ?? 'Store';
        $replyTo = $alertIdentity['reply_to'] ?? 'Noreply@mktg.booostr.co';
        $clubDisplayName = $alertIdentity['club_name'] ?? 'Booostr';
        $invoiceInfo = get_option('invoice_data', true);

        return $this->view('mail.adminsendmail')
            ->from($fromAddress, $fromName)
            ->replyTo($replyTo)
            ->subject($subject)
            ->with([
                'data' => $data,
                'subject' => $subject,
                'mailType' => $mailType,
                'clubDisplayName' => $clubDisplayName,
                'storeName' => $clubDisplayName,
                'invoice_data' => $invoiceInfo,
            ]);
    }
}
