<?php

use Inovector\Mixpost\Commands\ClearServicesCache;
use Inovector\Mixpost\Facades\Services;

it('will clear services cache', function () {
    Services::put('facebook', ['client_id' => '123', 'client_secret' => '456']);
    Services::put('twitter', ['client_id' => '11111', 'client_secret' => '22222', 'tier' => 'free']);

    $this->artisan(ClearServicesCache::class)->assertExitCode(0);

    expect(Services::getFromCache('facebook'))->toBeNull()
    ->and(Services::getFromCache('tiktok'))->toBeNull();
});
