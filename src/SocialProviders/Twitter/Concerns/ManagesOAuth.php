<?php

namespace Inovector\Mixpost\SocialProviders\Twitter\Concerns;

trait ManagesOAuth
{
    public function getAuthUrl(): string
    {
        $result = $this->connection->oauth('oauth/request_token', [
            'x_auth_access_type' => 'write',
            'oauth_callback' => $this->redirectUrl
        ]);

        return $this->connection->url('oauth/authorize', ['oauth_token' => $result['oauth_token']]);
    }

    public function requestAccessToken(array $params): array
    {
        $result = $this->connection->oauth('oauth/access_token', ['oauth_token' => $params['oauth_token'], 'oauth_verifier' => $params['oauth_verifier']]);

        return [
            'oauth_token' => $result['oauth_token'],
            'oauth_token_secret' => $result['oauth_token_secret']
        ];
    }

    // Overwrite setAccessToken to use Twitter SDK
    public function setAccessToken(array $token = []): void
    {
        $this->connection->setOauthToken($token['oauth_token'], $token['oauth_token_secret']);
    }

    // Overwrite useAccessToken to use Twitter SDK
    public function useAccessToken(array $token = []): static
    {
        $this->connection->setOauthToken($token['oauth_token'], $token['oauth_token_secret']);

        return $this;
    }
}
