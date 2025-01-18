<?php

namespace Inovector\Mixpost\SocialProviders\Mastodon\Concerns;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Support\SocialProviderResponse;

trait ManagesRateLimit
{
    /**
     * @param $response Response
     */
    public function buildResponse($response, Closure $okResult = null): SocialProviderResponse
    {
        $usage = $this->getRateLimitUsage($response->headers());

        $rateLimitAboutToBeExceeded = $usage['remaining'] < 5;
        $retryAfter = $rateLimitAboutToBeExceeded ? 5 * 60 : $usage['retry_after'];

        if (in_array($response->status(), [200, 201, 202])) {
            return $this->response(
                SocialProviderResponseStatus::OK,
                $okResult ? $okResult() : ($response->json() ?? []),
                $rateLimitAboutToBeExceeded,
                $retryAfter
            );
        }

        if ($response->status() === 429) {
            return $this->response(
                SocialProviderResponseStatus::EXCEEDED_RATE_LIMIT,
                $this->rateLimitExceedContext($usage['retry_after']),
                $rateLimitAboutToBeExceeded,
                $retryAfter
            );
        }

        if ($response->status() === 401) {
            return $this->response(
                SocialProviderResponseStatus::UNAUTHORIZED,
                ['access_token_expired']
            );
        }

        return $this->response(
            SocialProviderResponseStatus::ERROR,
            $response->json() ?? [],
            $rateLimitAboutToBeExceeded,
            $retryAfter
        );
    }

    /**
     * Rate limit
     *
     * @see https://docs.joinmastodon.org/api/rate-limits/#headers
     * @see https://docs.joinmastodon.org/client/intro/#http
     * @see https://docs.joinmastodon.org/entities/Error
     */
    public function getRateLimitUsage(array $headers): array
    {
        $headers = array_change_key_case($headers, CASE_LOWER);

        $timestampToRegainAccess = Carbon::parse(Arr::get($headers, 'x-ratelimit-reset.0'));

        return [
            'limit' => (int)Arr::get($headers, 'x-ratelimit-limit.0'),
            'remaining' => (int)Arr::get($headers, 'x-ratelimit-remaining.0'),
            'retry_after' => (int)Carbon::now('UTC')->diffInSeconds($timestampToRegainAccess),
        ];
    }
}
