<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Inovector\Mixpost\Facades\Settings as SettingsFacade;
use Inovector\Mixpost\Models\Setting as SettingModel;

class SaveSettings extends FormRequest
{
    public function rules(): array
    {
        return SettingsFacade::rules();
    }

    public function handle(): void
    {
        $schema = SettingsFacade::form();

        foreach ($schema as $name => $defaultPayload) {
            $payload = $this->input($name, $defaultPayload);

            SettingModel::updateOrCreate(['name' => $name], ['payload' => $payload]);

            SettingsFacade::put($name, $payload);
        }
    }
}
