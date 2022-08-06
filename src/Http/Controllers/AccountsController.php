<?php

namespace Lao9s\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Lao9s\Mixpost\Facades\SocialProviderManager;
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

    public function update(Account $account): \Illuminate\Http\RedirectResponse
    {
        $result = SocialProviderManager::connect($account->provider)->credentials($account->credentials)->getAccount();

        $account->update([
            'name' => $result['name'],
            'username' => $result['username'],
            'image' => $result['image']
        ]);

        return redirect()->back();
    }

    public function delete(Account $account): \Illuminate\Http\RedirectResponse
    {
        $account->delete();

        return redirect()->back();
    }
}
