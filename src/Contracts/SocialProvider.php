<?php

namespace Inovector\Mixpost\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Support\SocialProviderResponse;
use Closure;

interface SocialProvider
{
    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = []);

    public function identifier(): string;

    public static function name(): string;

    public static function service(): string;

    public function getAuthUrl(): string;

    public function requestAccessToken(array $params): array;

    public function useAccessToken(array $token = []): static;

    public function buildResponse($response, Closure $okResult = null): SocialProviderResponse;

    public function getAccount(): SocialProviderResponse;

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse;

    public function deletePost($id): SocialProviderResponse;

    public static function externalPostUrl(AccountResource $accountResource): string;
}
