<?php

namespace Inovector\Mixpost\Abstracts;

use Illuminate\Http\Request;
use Inovector\Mixpost\Contracts\SocialProvider as SocialProviderContract;
use Exception;

abstract class SocialProvider implements SocialProviderContract
{
    // For some social service providers, it is enough that the user himself can manage the content.
    // But some providers, such as Facebook, require a user account to select some entities to manage (pages or groups).
    // When you need to change this value, just overwrite it in the provider class that extends this class.
    // In case of `false` value, `getEntities()` method is required.
    public bool $onlyUserAccount = true;

    public array $callbackResponseKeys = [];
    protected array $accessToken = [];

    protected Request $request;
    protected string $clientId = '';
    protected string $clientSecret = '';
    protected string $redirectUrl;

    protected array $values = [];

    const ACCESS_TOKEN_SESSION_NAME = 'mixpost_provider_access_token';

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->request = $request;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        $this->values = $values;
    }

    public function isOnlyUserAccount(): bool
    {
        return $this->onlyUserAccount;
    }

    public function getCallbackResponse(): array
    {
        return $this->request->only($this->callbackResponseKeys);
    }

    // Use this method into web app. For example, in Controllers..etc.
    public function setAccessToken(array $token = []): void
    {
        $this->accessToken = $token;

        $this->request->session()->put(self::ACCESS_TOKEN_SESSION_NAME, $token);
    }

    public function getAccessToken()
    {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
        }

        $token = $this->request->session()->get(self::ACCESS_TOKEN_SESSION_NAME);

        if (!$token) {
            throw new Exception('Missing Access Token.');
        }

        return $token;
    }

    // Use this method outside of web app. For example, in Jobs..etc.
    public function useAccessToken(array $token = []): static
    {
        $this->accessToken = $token;

        return $this;
    }

    public function forgetAccessToken(): void
    {
        $this->accessToken = [];
        $this->request->session()->forget(self::ACCESS_TOKEN_SESSION_NAME);
    }

    public function buildUrlFromBase(string $url, array $params): string
    {
        return $url . '?' . http_build_query($params, '', '&');
    }
}
