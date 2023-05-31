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
     * Rate limit
     *
     * @see https://docs.joinmastodon.org/api/rate-limits/#headers
     * @see https://docs.joinmastodon.org/client/intro/#http
     * @see https://docs.joinmastodon.org/entities/Error
     */
    public function getRateLimitUsage(array $headers): array
    {
        $timestampToRegainAccess = Carbon::parse(Arr::get($headers, 'X-RateLimit-Reset.0'));

        return [
            'limit' => intval(Arr::get($headers, 'X-RateLimit-Limit.0')),
            'remaining' => intval(Arr::get($headers, 'X-RateLimit-Remaining.0')),
            'retry_after' => Carbon::now('UTC')->diffInSeconds($timestampToRegainAccess),
        ];
    }

    /**
     * @param $response Response
     */
    public function buildResponse($response, Closure $okResult = null): SocialProviderResponse
    {
        $usage = $this->getRateLimitUsage($response->headers());

        $rateLimitAboutToBeExceeded = $usage['remaining'] < 5;
        $retryAfter = $rateLimitAboutToBeExceeded ? 5 * 60 : $usage['retry_after'];

        if (in_array($response->status(), [200, 201])) {
            return $this->response(
                SocialProviderResponseStatus::OK,
                $okResult ? $okResult() : $response->json(),
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

        return $this->response(
            SocialProviderResponseStatus::ERROR,
            $response->json(),
            $rateLimitAboutToBeExceeded,
            $retryAfter
        );
    }
}
