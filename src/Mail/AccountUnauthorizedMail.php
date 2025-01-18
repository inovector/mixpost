<?php

namespace Inovector\Mixpost\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Models\Account;

class AccountUnauthorizedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public function __construct(public readonly Account $account)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We’ve Lost Connection - Action Required',
        );
    }

    public function content(): Content
    {
        $url = url(
            route('mixpost.accounts.index', false)
        );

        return new Content(
            view: 'mixpost::mail.accountUnauthorized',
            with: [
                'account' => $this->account,
                'url' => $url,
            ],
        );
    }
}
