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
use Inovector\Mixpost\Support\SocialProviderResponse;

class AccountEntitiesController extends Controller
{
    public function index(Request $request): RedirectResponse|Response
    {
        $providerName = $request->route('provider');

        if (!$request->session()->has('mixpost_callback_response')) {
            return redirect()->route('mixpost.accounts.index');
        }

        $provider = SocialProviderManager::connect($providerName);

        $accessToken = $provider->requestAccessToken($request->session()->get('mixpost_callback_response'));

        if ($error = Arr::get($accessToken, 'error')) {
            return redirect()->route('mixpost.accounts.index')
                ->with('error', $error);
        }

        $provider->setAccessToken($accessToken);

        /** @var SocialProviderResponse $response * */
        $response = $provider->getEntities();

        if ($response->hasError()) {
            return redirect()->route('mixpost.accounts.index')
                ->with('warning', "It's something wrong. Try again.");
        }

        $existingAccounts = Account::select('provider', 'provider_id')->get();

        $entities = collect($response->context())->map(function ($entity) use ($providerName, $existingAccounts) {
            $entity['connected'] = !!$existingAccounts
                ->where('provider', $providerName)
                ->where('provider_id', $entity['id'])
                ->first();

            return $entity;
        })->sort(function ($account) {
            return $account['connected'];
        })->values();

        if (empty($entities)) {
            return redirect()->route('mixpost.accounts.index')
                ->with('warning', 'The account has no entities.');
        }

        return Inertia::render('Accounts/AccountEntities', [
            'provider' => $providerName,
            'entities' => $entities
        ]);
    }

    public function store(StoreProviderEntities $storeAccountEntities): RedirectResponse
    {
        $storeAccountEntities->handle();

        return redirect()->route('mixpost.accounts.index');
    }
}
