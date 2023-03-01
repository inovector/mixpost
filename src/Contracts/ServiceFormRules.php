<?php

namespace Inovector\Mixpost\Contracts;

interface ServiceFormRules
{
    public static function form(): array;

    public static function rules(): array;

    public static function messages(): array;
}
