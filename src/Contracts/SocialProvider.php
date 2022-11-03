<?php

namespace Inovector\Mixpost\Contracts;

use Illuminate\Http\Request;

interface SocialProvider
{
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl);

    public function setApiVersion(): void;

    public function credentials(): static;

    public function setCredentials(): void;

    public function getAuthUrl(): string;

    public function getAccessToken(): array;

    public function getAccount();

    public function publishPost($text, $media = []);

    public function deletePost();

    public function getMetrics();
}
