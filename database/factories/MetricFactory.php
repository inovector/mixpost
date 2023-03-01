<?php

namespace Inovector\Mixpost\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Metric;

class MetricFactory extends Factory
{
    protected $model = Metric::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'data' => [
                'likes' => $this->faker->randomDigit(),
                'retweets' => $this->faker->randomDigit(),
                'impressions' => $this->faker->randomDigit()
            ],
            'date' => $this->faker->dateTimeBetween('-90 days')
        ];
    }
}
