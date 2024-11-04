<?php

namespace Inovector\Mixpost\SocialProviders\Twitter\Concerns;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Arr;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Support\SocialProviderResponse;

trait ManagesRateLimit
{
    /**
     * Rate limit
     *
     * @see https://developer.twitter.com/en/docs/twitter-api/rate-limits
     */
    public function getRateLimitUsage(): array
    {
        $headers = $this->connection->getLastXHeaders();

        // In case there is incompatibility between the app credentials and the selected Tier,
        // Twitter no longer returns data about limit rates.
        // To avoid blocking script for a long period of time (`retry_after` may have too large a value),
        // we will set some dummy data and the script will skip the rate limit check
        if (!Arr::has($headers, ['x_rate_limit_reset', 'x_rate_limit_limit', 'x_rate_limit_remaining'])) {
            return [
                'limit' => 100,
                'remaining' => 99,
                'retry_after' => 300
            ];
        }

        $timeToRegainAccess = Carbon::parse((int)Arr::get($headers, 'x_rate_limit_reset', 0));

        return [
            'limit' => intval(Arr::get($headers, 'x_rate_limit_limit')),
            'remaining' => intval(Arr::get($headers, 'x_rate_limit_remaining', 0)),
            'retry_after' => (int)Carbon::now('UTC')->diffInSeconds($timeToRegainAccess),
        ];
    }

    /**
     * @param $response array|object
     */
    public function buildResponse($response, Closure $okResult = null): SocialProviderResponse
    {
        $usage = $this->getRateLimitUsage();
        $rateLimitAboutToBeExceeded = $usage['remaining'] < 5;

        if (isset($response->status) && $response->status === 401) {
            return $this->response(
                SocialProviderResponseStatus::UNAUTHORIZED,
                ['access_token_expired']
            );
        }

        if (isset($response->status) && $response->status === 429) {
            return $this->response(
                SocialProviderResponseStatus::EXCEEDED_RATE_LIMIT,
                $this->rateLimitExceedContext($usage['retry_after']),
                $rateLimitAboutToBeExceeded,
                $usage['retry_after']
            );
        }

        if (isset($response->status) && $response->status === 403) {
            return $this->response(
                SocialProviderResponseStatus::ERROR,
                ['detail' => $response->detail],
                $rateLimitAboutToBeExceeded,
                $usage['retry_after']
            );
        }

        if (!isset($response->errors)) {
            return $this->response(
                SocialProviderResponseStatus::OK,
                $okResult ? $okResult() : Arr::wrap($response),
                $rateLimitAboutToBeExceeded,
                $usage['retry_after']
            );
        }

        return $this->response(
            SocialProviderResponseStatus::ERROR,
            Arr::wrap($response),
            $rateLimitAboutToBeExceeded,
            $usage['retry_after']
        );
    }
}
