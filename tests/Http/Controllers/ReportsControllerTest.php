<?php

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Audience;
use Inovector\Mixpost\Models\Metric;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

test('display reports', function () {
    $this->actingAs(test()->user);

    $account = Account::factory()->create();

    $today = Carbon::now('UTC');
    $yesterday = Carbon::yesterday('UTC');
    $subWeek = Carbon::now('UTC')->subWeek();
    $subTwoWeeks = Carbon::now('UTC')->subWeeks(2);

    Audience::factory()->state([
        'account_id' => $account->id,
        'date' => $yesterday,
        'total' => 0,
    ])->create();

    Audience::factory()->state([
        'account_id' => $account->id,
        'date' => $subWeek,
        'total' => 28,
    ])->create();

    Metric::factory()->state([
        'account_id' => $account->id,
    ])->create();

    $response = $this->getJson(route('mixpost.reports', ['account_id' => $account->id, 'period' => '30_days']))
        ->assertStatus(200)
        ->assertJsonStructure([
            'metrics',
            'audience' => [
                'labels',
                'values',
            ],
        ]);

    $audienceResult = $response->json('audience');

    $period = CarbonPeriod::create(Carbon::now('UTC')->subDays(29), Carbon::now('UTC'));

    foreach ($period as $index => $item) {
        // Check value for Today
        if ($item->toDateString() === $today->toDateString()) {
            expect($audienceResult['values'][$index])->toBeNull();
        }

        // Check value for Yesterday
        if ($item->toDateString() === $yesterday->toDateString()) {
            expect($audienceResult['values'][$index])->toEqual(0);
        }

        // Check value for a week ago
        if ($item->toDateString() === $subWeek->toDateString()) {
            expect($audienceResult['values'][$index])->toEqual(28);
        }

        // Check value for 2 weeks ago
        if ($item->toDateString() === $subTwoWeeks->toDateString()) {
            expect($audienceResult['values'][$index])->toBeNull();
        }
    }
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
