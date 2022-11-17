<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Inovector\Mixpost\Actions\StoreProviderEntitiesAsAccounts as StoreProviderEntitiesAsAccountsAction;

class StoreProviderEntities extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
        ];
    }

    public function handle()
    {
        (new StoreProviderEntitiesAsAccountsAction())($this->route('provider'), $this->input('items'));
    }
}
