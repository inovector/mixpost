<?php

use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Models\User;
use Inovector\Mixpost\Models\Setting;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('can update the settings', function () {
    $this->actingAs(test()->user);

    $data = [
        'timezone' => 'Europe/Chisinau',
        'time_format' => 12,
        'week_starts_on' => 1,
    ];

    $this->putJson(route('mixpost.settings.update'), $data)->assertStatus(302);

    expect(Setting::whereIn('name', ['timezone', 'time_format', 'week_starts_on'])->count() === 3)->toBeTrue()
        ->and(Settings::getFromCache('timezone'))->toEqual($data['timezone'])
        ->and(Settings::getFromCache('time_format'))->toEqual($data['time_format'])
        ->and(Settings::getFromCache('week_starts_on'))->toEqual($data['week_starts_on']);
});

it('can show validation on update the settings', function () {
    $this->actingAs(test()->user);

    $this->putJson(route('mixpost.settings.update'))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['timezone', 'time_format', 'week_starts_on']);
});

it('can prevent unauthorized users to update the settings', function () {
    $this->putJson(route('mixpost.settings.update'))->assertUnauthorized();
});

