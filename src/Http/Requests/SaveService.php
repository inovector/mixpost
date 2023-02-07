<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Inovector\Mixpost\Facades\Services as ServicesFacade;
use Inovector\Mixpost\Models\Service as ServiceModel;

class SaveService extends FormRequest
{
    public function rules(): array
    {
        return ServicesFacade::rules($this->route('service'));
    }

    public function handle(): void
    {
        $name = $this->route('service');

        $value = [
            'client_id' => $this->input("client_id"),
            'client_secret' => $this->input("client_secret"),
        ];

        (new UpdateOrCreateService())($name, $value);
    }

    public function messages(): array
    {
        return ServicesFacade::messages($this->route('service'));
    }
}
