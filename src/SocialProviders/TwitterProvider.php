<?php

namespace Inovector\Mixpost\SocialProviders;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inovector\Mixpost\Abstracts\SocialProvider;

class TwitterProvider extends SocialProvider
{
    const DEFAULT_API_VERSION = '2';
    const STANDARD_API_VERSION = '1.1';

    public TwitterOAuth $connection;

    // Overwrite __construct to use Twitter SDK
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl)
    {
        $this->connection = new TwitterOAuth($clientId, $clientSecret);
        $this->connection->setApiVersion(self::DEFAULT_API_VERSION);

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);
    }

    public function getAuthUrl(): string
    {
        $response = $this->connection->oauth('oauth/request_token', ['x_auth_access_type' => 'write', 'redirect_uri' => $this->redirectUrl]);

        return $this->connection->url('oauth/authorize', ['oauth_token' => $response['oauth_token'], 'oauth_token_secret' => $response['oauth_token_secret']]);
    }

    public function requestAccessToken(array $params = []): array
    {
        $response = $this->connection->oauth('oauth/access_token', ['oauth_token' => $this->request->get('oauth_token'), 'oauth_verifier' => $this->request->get('oauth_verifier')]);

        return [
            'oauth_token' => $response['oauth_token'],
            'oauth_token_secret' => $response['oauth_token_secret']
        ];
    }

    // Overwrite setAccessToken to use Twitter SDK
    public function setAccessToken(array $token = []): void
    {
        $this->connection->setOauthToken($token['oauth_token'], $token['oauth_token_secret']);
    }

    public function getAccount(array $params = []): array
    {
        $response = $this->connection->get('users/me', ['user.fields' => 'profile_image_url,created_at']);

        return [
            'id' => $response->data->id,
            'name' => $response->data->name,
            'username' => $response->data->username,
            'image' => str_replace('normal', '400x400', $response->data->profile_image_url)
        ];
    }

    public function publishPost(string $text, array $media = [], array $params = []): array
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

        if (isset($postResponse->status) && $postResponse->status === 403) {
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
