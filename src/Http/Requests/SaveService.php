<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Inovector\Mixpost\Facades\ServiceManager;

class SaveService extends FormRequest
{
    public function rules(): array
    {
        $default = [
            'active' => ['required', 'boolean'],
        ];

        $formRules = $this->service()::formRules();
        $modifiedFormRules = array_reduce(array_keys($formRules), function ($carry, $key) use ($formRules) {
            $carry["configuration.$key"] = $formRules[$key];
            return $carry;
        }, []);

        return array_merge(
            $default,
            $modifiedFormRules
        );
    }

    public function handle(): void
    {
        $configuration = Arr::map($this->service()::form(), function ($_, $key) {
            return $this->input("configuration.$key");
        });

        (new UpdateOrCreateService())(
            name: $this->route('service'),
            configuration: $configuration,
            active: $this->input('active', false)
        );
    }

    public function messages(): array
    {
        $formMessages = $this->service()::formMessages();

        return array_reduce(array_keys($formMessages), function ($carry, $key) use ($formMessages) {
            $carry["configuration.$key"] = $formMessages[$key];
            return $carry;
        }, []);
    }

    protected function service(): ?string
    {
        return ServiceManager::getServiceClass($this->route('service'));
    }
}
