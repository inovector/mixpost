<?php

namespace Inovector\Mixpost\Concerns\Job;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Cache;

trait HasSocialProviderJobRateLimit
{
    public $tries = 0;

    public $maxExceptions = 1;

    public function retryUntil(): DateTime
    {
        return Carbon::now('UTC')->addHours(24);
    }

    public function getRateLimitCacheKey(bool $isAppLevel = false): string
    {
        if ($isAppLevel) {
            $platform = match ($this->account->provider) {
                'facebook_page' => 'meta',
                'facebook_group' => 'meta',
                default => $this->account->provider,
            };

            return "mixpost-$platform-api-limit";
        }

        return "mixpost-{$this->account->id}-api-limit";
    }

    public function rateLimitExpiration()
    {
        if ($timestamp = Cache::get($this->getRateLimitCacheKey(true))) {
            return $timestamp - time();
        }

        if ($timestamp = Cache::get($this->getRateLimitCacheKey())) {
            return $timestamp - time();
        }

        return null;
    }

    public function storeRateLimitExceeded(int $secondsRemaining, bool $isAppLevel = false): void
    {
        Cache::put(
            $this->getRateLimitCacheKey($isAppLevel),
            now()->addSeconds($secondsRemaining)->timestamp,
            $secondsRemaining
        );
    }
}
