<?php

namespace Inovector\Mixpost\SocialProviders;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inovector\Mixpost\Contracts\SocialProvider;

class TwitterProvider implements SocialProvider
{
    const DEFAULT_API_VERSION = '2';
    const STANDARD_API_VERSION = '1.1';

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
            'username' => $response->data->username,
            'image' => str_replace('normal', '400x400', $response->data->profile_image_url)
        ];
    }

    public function publishPost($text, $media = [])
    {
        // Upload media
        $this->connection->setApiVersion(self::STANDARD_API_VERSION);

        $uploadedMediaIds = [];
        $uploadMediaErrors = [];

        foreach ($media as $item) {
            $parameters = [
                'media' => $item['path'],
            ];

            $uploadResponse = $this->connection->upload('media/upload', $parameters);

            if (!$uploadResponse) {
                $uploadMediaErrors[$item['id']] = $uploadResponse;
                continue;
            }

            $uploadedMediaIds[] = $uploadResponse->media_id_string;
        }

        // Publish post with media
        $this->connection->setApiVersion(self::DEFAULT_API_VERSION);

        $postParameters = ['text' => $text];

        if (!empty($uploadedMediaIds)) {
            $postParameters['media'] = [
                'media_ids' => $uploadedMediaIds
            ];
        }

        $postResponse = $this->connection->post('tweets', $postParameters, true);

        $errors = Arr::map($postResponse->errors ?? [], function ($error) {
            return $error->message;
        });

        if ($postResponse->status === 403) {
            $errors[] = $postResponse->detail;
        }

        return [
            'errors' => $errors,
            'upload_media_error' => $uploadMediaErrors,
        ];
    }

    public function deletePost()
    {
        // TODO: Implement deletePost() method.
    }

    public function getMetrics()
    {
        // TODO: Implement metrics() method.
    }
}
