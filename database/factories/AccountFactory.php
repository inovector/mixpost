<?php

namespace Inovector\Mixpost\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Inovector\Mixpost\Models\Account;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        $providers = ['twitter', 'mastodon', 'facebook_page', 'facebook_group'];

        $name = $this->faker->name;

        return [
            'name' => $name,
            'username' => Str::camel($this->faker->name),
            'provider' => $providers[rand(0, 3)],
            'provider_id' => Str::random(),
            'media' => ['disk' => 'public', 'path' => '/'],
            'access_token' => ['auth_token' => Str::random()]
        ];
    }
}
