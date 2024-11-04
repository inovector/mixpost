<?php

namespace Inovector\Mixpost\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Inovector\Mixpost\Models\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'name' => $this->faker->domainName,
            'configuration' => ['client_id' => $this->faker->randomDigit(), 'client_secret' => $this->faker->randomDigit()],
            'active' => $this->faker->boolean(),
        ];
    }
}
