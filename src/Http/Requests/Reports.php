<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Inovector\Mixpost\Reports\FacebookPageReports;
use Inovector\Mixpost\Reports\MastodonReports;
use Inovector\Mixpost\Reports\TwitterReports;
use Inovector\Mixpost\Contracts\ProviderReports;
use Inovector\Mixpost\Models\Account;

class Reports extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'integer', 'exists:mixpost_accounts,id'],
            'period' => ['required', 'string', Rule::in(['7_days', '30_days', '90_days'])]
        ];
    }

    public function handle(): array
    {
        $account = Account::find($this->get('account_id'));

        $providerReports = match ($account->provider) {
            'twitter' => TwitterReports::class,
            'facebook_page' => FacebookPageReports::class,
            'mastodon' => MastodonReports::class,
            default => null
        };

        if (!$providerReports) {
            return [];
        }

        $providerReports = (new $providerReports());

        if (!$providerReports instanceof ProviderReports) {
            throw new \Exception('The provider reports must be an instance of ProviderReports');
        }

        return $providerReports($account, $this->get('period', ''));
    }
}
