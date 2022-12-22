<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Inovector\Mixpost\Facades\Settings;

class Calendar extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function handle()
    {
        $this->merge([
            'calendar_type' => $this->type(),
            'date' => $this->route('date', $this->today()),
            'exclude_status' => 'draft',
        ]);
    }

    public function today(): string
    {
        return now()->tz(Settings::get('timezone'))->toDateString();
    }

    public function type(): string
    {
        if ($type = $this->route('type')) {
            Settings::put('calendar_type', $this->route('type'));

            return $type;
        }

        return Settings::getFromCache('calendar_type', 'month');
    }
}
