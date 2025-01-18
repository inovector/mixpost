<?php

use Inovector\Mixpost\Commands\ClearServicesCache;
use Inovector\Mixpost\Facades\ServiceManager;

it('will clear services cache', function () {
    ServiceManager::put('facebook', ['client_id' => '123', 'client_secret' => '456']);
    ServiceManager::put('twitter', ['client_id' => '11111', 'client_secret' => '22222', 'tier' => 'free']);

    $this->artisan(ClearServicesCache::class)->assertExitCode(0);

    expect(ServiceManager::getFromCache('facebook'))->toBeNull()
    ->and(ServiceManager::getFromCache('tiktok'))->toBeNull();
});
