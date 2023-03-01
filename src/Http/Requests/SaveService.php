<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Inovector\Mixpost\Facades\Services as ServicesFacade;
use Inovector\Mixpost\Models\Service as ServiceModel;

class SaveService extends FormRequest
{
    public function rules(): array
    {
        $keys = array_keys(ServicesFacade::form());

        $serviceRules = ServicesFacade::rules($this->route('service'));

        return array_merge($serviceRules, [Rule::in($keys)]);
    }

    public function handle(): void
    {
        $name = $this->route('service');

        $form = ServicesFacade::form()[$name];

        $value = Arr::map($form, function ($item, $key) {
            return $this->input($key);
        });

        (new UpdateOrCreateService())($name, $value);
    }

    public function messages(): array
    {
        return ServicesFacade::messages($this->route('service'));
    }
}
