<?php

namespace Inovector\Mixpost\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Inovector\Mixpost\Events\AccountUnauthorized as AccountUnauthorizedEvent;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Mail\AccountUnauthorizedMail;

class SendAccountUnauthorizedNotification implements ShouldQueue
{
    public function handle(AccountUnauthorizedEvent $event): void
    {
        $adminEmail = Settings::get('admin_email');

        if (!$adminEmail) {
            return;
        }

        Mail::to($adminEmail)->send(new AccountUnauthorizedMail($event->account));
    }
}
