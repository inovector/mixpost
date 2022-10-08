<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Inovector\Mixpost\Model\Tag;
use Inovector\Mixpost\Rules\HexRule;

class StoreTag extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'hex_color' => ['required', new HexRule()]
        ];
    }

    public function handle()
    {
        return Tag::insert([
            'name' => $this->input('name'),
            'hex_color' => Str::after($this->input('hex_color'), '#'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
