<?php

namespace Inovector\Mixpost\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Models\Account;

class AccountUnauthorized
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Account $account)
    {
    }
}
