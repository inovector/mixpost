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
        $providers = ['twitter', 'mastodon', 'facebook_page'];

        $name = $this->faker->name;

        return [
            'uuid' => $this->faker->uuid,
            'name' => $name,
            'username' => Str::camel($this->faker->name),
            'provider' => $providers[rand(0, 2)],
            'provider_id' => Str::random(),
            'media' => ['disk' => 'public', 'path' => '/'],
            'data' => null,
            'authorized' => true,
            'access_token' => ['access_token' => Str::random()]
        ];
    }
}
