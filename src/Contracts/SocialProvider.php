<?php

namespace Inovector\Mixpost\Contracts;

use Illuminate\Http\Request;

interface SocialProvider
{
    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = []);

    public function getAuthUrl(): string;

    public function requestAccessToken(array $params): array;

    public function useAccessToken(array $token = []): static;

    public function getAccount();

    public function publishPost(string $text, array $media = [], array $params = []);

    public function deletePost();
}
