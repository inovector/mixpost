<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Actions\UpdateOrCreateAccount;
use Inovector\Mixpost\Concerns\UsesSocialProviderManager;
use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Models\Account;

class AccountsController extends Controller
{
    use UsesSocialProviderManager;

    public function index(): Response
    {
        return Inertia::render('Accounts/Accounts', [
            'accounts' => AccountResource::collection(Account::latest()->get())->resolve(),
            'has_service' => [
                'twitter' => !!Services::get('twitter', 'client_id'),
                'facebook' => !!Services::get('facebook', 'client_id')
            ]
        ]);
    }

    public function update(Account $account): RedirectResponse
    {
        $connection = $this->connectProvider($account);

        $response = $connection->getAccount();

        if ($response->hasError()) {
            if ($response->isUnauthorized()) {
                return redirect()->back()->with('error', 'The account cannot be updated. Re-authenticate your account.');
            }

            return redirect()->back()->with('error', 'The account cannot be updated.');
        }

        (new UpdateOrCreateAccount())($account->provider, $response->context(), $account->access_token->toArray());

        return redirect()->back();
    }

    public function delete(Account $account): RedirectResponse
    {
        $connection = $this->connectProvider($account);

        if (method_exists($connection, 'revokeToken')) {
            $connection->revokeToken();
        }

        $account->delete();

        return redirect()->back();
    }
}
