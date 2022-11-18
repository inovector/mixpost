<?php

namespace Inovector\Mixpost\Contracts;

use Illuminate\Http\Request;

interface SocialProvider
{
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl);

    public function getAuthUrl(): string;

    public function requestAccessToken(array $params = []): array;

    public function useAccessToken(array $token = []): static;

    public function getAccount(array $params = []);

    public function publishPost(string $text, array $media = [], array $params = []);

    public function deletePost();

    public function getMetrics();
}
