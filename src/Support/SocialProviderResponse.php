<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Arr;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;

class SocialProviderResponse
{
    public function __construct(
        private readonly SocialProviderResponseStatus $status,
        private array                                 $context,
        private readonly bool                         $rateLimitAboutToBeExceeded = false,
        private readonly int                          $retryAfter = 0,
        private readonly bool                         $isAppLevel = false
    )
    {
    }

    public function __get(string $key)
    {
        return Arr::get($this->context, $key);
    }

    public function status(): SocialProviderResponseStatus
    {
        return $this->status;
    }

    public function context(): array
    {
        return $this->context;
    }

    public function id()
    {
        return Arr::get($this->context, 'id');
    }

    public function retryAfter(): int
    {
        return $this->retryAfter;
    }

    public function isOk(): bool
    {
        return $this->status->value === SocialProviderResponseStatus::OK->value;
    }

    public function hasError(): bool
    {
        return !$this->isOk();
    }

    public function isUnauthorized(): bool
    {
        return $this->status->value === SocialProviderResponseStatus::UNAUTHORIZED->value;
    }

    public function hasExceededRateLimit(): bool
    {
        return $this->status->value === SocialProviderResponseStatus::EXCEEDED_RATE_LIMIT->value;
    }

    public function rateLimitAboutToBeExceeded(): bool
    {
        return $this->rateLimitAboutToBeExceeded;
    }

    public function isAppLevel(): bool
    {
        return $this->isAppLevel;
    }

    public function useContext(array $value): static
    {
        $this->context = $value;

        return $this;
    }
}
