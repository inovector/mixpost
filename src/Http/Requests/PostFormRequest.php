<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'accounts' => ['array'],
            'accounts.*' => ['integer'],
            'tags' => ['array'],
            'tags.*' => ['integer'],
            'date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'time' => ['nullable', 'date_format:H:i'],
            'versions' => ['required', 'array', 'min:1'],
            'versions.*.content.*.body' => ['nullable', 'string', 'max:5000'],
            'versions.*.content.*.media' => ['array'],
            'versions.*.content.*.media.*' => ['integer']
        ];
    }

    protected function scheduledAt(): ?string
    {
        return $this->input('date') && $this->input('time') ? "{$this->input('date')} {$this->input('time')}" : null;
    }
}
