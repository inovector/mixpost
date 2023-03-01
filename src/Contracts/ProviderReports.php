<?php

namespace Inovector\Mixpost\Contracts;

use Inovector\Mixpost\Models\Account;

interface ProviderReports
{
    public function __invoke(Account $account, string $period): array;
}
