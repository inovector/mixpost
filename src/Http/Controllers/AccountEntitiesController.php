<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Http\Requests\StoreProviderEntities;
use Inovector\Mixpost\Models\Account;

class AccountEntitiesController extends Controller
{
    public function index(Request $request, $providerName): RedirectResponse|Response
    {
        if (!$request->session()->has('mixpost_callback_response')) {
            return redirect()->route('mixpost.accounts.index');
        }

        $provider = SocialProviderManager::connect($providerName);

        $accessToken = $provider->requestAccessToken($request->session()->get('mixpost_callback_response'));

        if ($accessToken['error'] ?? null) {
            return redirect()->route('mixpost.accounts.index')->with('error', $accessToken['error']);
        }

        $provider->setAccessToken($accessToken);

        $existingAccounts = Account::select('provider', 'provider_id')->get();

        $accounts = array_values(Arr::where($provider->getEntities(), function ($account) use ($providerName, $existingAccounts) {
            return !$existingAccounts->where('provider', $providerName)->where('provider_id', $account['id'])->first();
        }));

        if (empty($accounts)) {
            return redirect()->route('mixpost.accounts.index')->with('warning', 'The account has no pages or all available pages are already added.');
        }

        return Inertia::render('Accounts/AccountEntities', [
            'provider' => $providerName,
            'entities' => $accounts
        ]);
    }

    public function store(StoreProviderEntities $storeAccountEntities): RedirectResponse
    {
        $storeAccountEntities->handle();

        return redirect()->route('mixpost.accounts.index');
    }
}
