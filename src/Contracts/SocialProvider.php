<?php

namespace Lao9s\Mixpost\Contracts;

use Illuminate\Http\Request;
use Lao9s\Mixpost\Model\Post;

interface SocialProvider
{
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl);

    public function setApiVersion(): void;

    public function credentials(): static;

    public function setCredentials(): void;

    public function getAuthUrl(): string;

    public function getAccessToken(): array;

    public function getAccount();

    public function post(Post $post);

    public function getMetrics();
}
