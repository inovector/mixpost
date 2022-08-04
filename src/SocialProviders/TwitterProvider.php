<?php

namespace Lao9s\Mixpost\SocialProviders;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use Lao9s\Mixpost\Contracts\SocialProvider;
use Lao9s\Mixpost\Model\Post;

class TwitterProvider implements SocialProvider
{
    const DEFAULT_API_VERSION = '2';

    public TwitterOAuth $connection;
    protected Request $request;
    protected string $redirectUrl;

    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl)
    {
        $connection = new TwitterOAuth($clientId, $clientSecret);
        $connection->setApiVersion(self::DEFAULT_API_VERSION);

        $this->request = $request;
        $this->redirectUrl = $redirectUrl;
        $this->connection = $connection;
    }

    public function credentials(array $accountCredentials = []): static
    {
        $this->connection->setOauthToken($accountCredentials['oauth_token'], $accountCredentials['oauth_token_secret']);

        return $this;
    }

    public function setApiVersion(string $apiVersion = '2'): void
    {
        $this->connection->setApiVersion($apiVersion);
    }

    public function setCredentials(array $accountCredentials = []): void
    {
        $this->connection->setOauthToken($accountCredentials['oauth_token'], $accountCredentials['oauth_token_secret']);
    }

    public function getAuthUrl(): string
    {
        $response = $this->connection->oauth('oauth/request_token', ['x_auth_access_type' => 'write', 'redirect_uri' => $this->redirectUrl]);

        return $this->connection->url('oauth/authorize', ['oauth_token' => $response['oauth_token'], 'oauth_token_secret' => $response['oauth_token_secret']]);
    }

    public function getAccessToken(): array
    {
        $response = $this->connection->oauth('oauth/access_token', ['oauth_token' => $this->request->get('oauth_token'), 'oauth_verifier' => $this->request->get('oauth_verifier')]);

        return [
            'oauth_token' => $response['oauth_token'],
            'oauth_token_secret' => $response['oauth_token_secret']
        ];
    }

    public function getAccount(): array
    {
        $response = $this->connection->get('users/me', ['user.fields' => 'profile_image_url,created_at']);

        return [
            'id' => $response->data->id,
            'name' => $response->data->name,
            'image' => str_replace('normal', '400x400', $response->data->profile_image_url)
        ];
    }

    public function post(Post $post)
    {
        // TODO: Implement post() method.
    }

    public function getMetrics()
    {
        // TODO: Implement metrics() method.
    }
}
