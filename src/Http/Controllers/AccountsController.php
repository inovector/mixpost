<?php

namespace Lao9s\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Lao9s\Mixpost\Model\Account;
use Lao9s\Mixpost\Resources\AccountResource;

class AccountsController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Accounts', [
            'accounts' => AccountResource::collection(Account::oldest()->get())->resolve()
        ]);
    }

    public function delete()
    {

    }
}
