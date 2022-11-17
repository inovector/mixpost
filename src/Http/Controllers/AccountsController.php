<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Models\Account;

class AccountsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Accounts/Accounts', [
            'accounts' => AccountResource::collection(Account::latest()->get())->resolve()
        ]);
    }

    public function update(Account $account): RedirectResponse
    {
        $provider = SocialProviderManager::connect($account->provider);
        $provider->setAccessToken($account->access_token);

        $result = $provider->getAccount($account->toArray());

        if (empty($result)) {
            return redirect()->back()->with('The account cannot be updated. Re-authenticate your account.');
        }

        $account->update([
            'name' => $result['name'],
            'username' => $result['username'],
            'image' => $result['image']
        ]);

        return redirect()->back();
    }

    public function delete(Account $account): RedirectResponse
    {
        $account->delete();

        return redirect()->back();
    }
}
