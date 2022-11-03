<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Inovector\Mixpost\Facades\Settings as SettingsFacade;
use Inovector\Mixpost\Models\Setting as SettingModel;

class SaveSettings extends FormRequest
{
    public function rules(): array
    {
        return [
            'timezone' => ['required', 'timezone'],
            'time_format' => ['required', Rule::in([12, 24])],
            'week_starts_on' => ['required', Rule::in([0, 1])],
        ];
    }

    public function handle(): void
    {
        $schema = SettingsFacade::schema();

        foreach ($schema as $name => $defaultPayload) {
            $payload = $this->input($name, $defaultPayload);

            SettingModel::updateOrCreate(['name' => $name], ['payload' => $payload]);

            SettingsFacade::put($name, $payload);
        }
    }
}
