<?php

use Inovector\Mixpost\Models\User;
use Inovector\Mixpost\Models\Metric;

beforeEach(function () {
    test()->user = User::factory()->create();
});

test('display reports', function () {
    $this->actingAs(test()->user);

    $metric = Metric::factory()->create();

    $this->getJson(route('mixpost.reports', ['account_id' => $metric->account_id, 'period' => '90_days']))
        ->assertStatus(200)
        ->assertJsonStructure([
            'metrics',
            'audience' => [
                'labels',
                'values',
            ]
        ]);
});

it('can show validation on getting reports', function () {
    $this->actingAs(test()->user);

    $this->getJson(route('mixpost.reports'))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['account_id', 'period']);
});

it('can prevent unauthorized users to see the reports', function () {
    $this->getJson(route('mixpost.reports'))->assertUnauthorized();
});
