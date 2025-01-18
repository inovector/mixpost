<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inovector\Mixpost\Models\Tag;
use Inovector\Mixpost\Rules\HexRule;

class UpdateTag extends FormRequest
{
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['name', 'color'])],
            'name' => [Rule::requiredIf($this->input('action') === 'name'), 'string', 'max:255'],
            'hex_color' => [Rule::requiredIf($this->input('action') === 'color'), new HexRule()],
        ];
    }

    public function handle(): int
    {
        $record = Tag::firstOrFailByUuid($this->route('tag'));

        if ($this->input('action') === 'name') {
            return $record->update($this->only('name'));
        }

        if ($this->input('action') === 'color') {
            return $record->update([
                'hex_color' => Str::after($this->input('hex_color'), '#'),
            ]);
        }

        return 0;
    }
}
