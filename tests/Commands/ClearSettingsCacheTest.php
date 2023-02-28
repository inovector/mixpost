<?php

use Inovector\Mixpost\Commands\ClearSettingsCache;
use Inovector\Mixpost\Facades\Settings;

it('will clear settings cache', function () {
    Settings::put('timezone', 'UTC');
    Settings::put('date_format', 'human');

    $this->artisan(ClearSettingsCache::class)->assertExitCode(0);

    expect(Settings::getFromCache('timezone') === null)->toBeTrue()
        ->and(Settings::getFromCache('date_format') === null)->toBeTrue();
});
