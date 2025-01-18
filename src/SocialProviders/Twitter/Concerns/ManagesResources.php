<?php

namespace Inovector\Mixpost\SocialProviders\Twitter\Concerns;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Support\SocialProviderResponse;
use Exception;

trait ManagesResources
{
    public function getAccount(): SocialProviderResponse
    {
        $response = $this->connection->get('users/me', ['user.fields' => 'profile_image_url,created_at']);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'id' => $response->data->id,
                'name' => $response->data->name,
                'username' => $response->data->username,
                'image' => str_replace('normal', '400x400', $response->data->profile_image_url)
            ];
        });
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        try {
            $mediaResult = $this->uploadMedia($media);
        } catch (Exception $exception) {
            return $this->response(SocialProviderResponseStatus::ERROR, [$exception->getMessage()]);
        }

        if (!empty($mediaResult['errors'])) {
            return $this->response(SocialProviderResponseStatus::ERROR, $mediaResult['errors']);
        }

        return match ($this->getTier()) {
            'legacy' => $this->storePostWithApiV1($text, $mediaResult),
            default => $this->storePostWithApiV2($text, $mediaResult),
        };
    }

    protected function storePostWithApiV1(string $text, array $mediaResult): SocialProviderResponse
    {
        $this->connection->setApiVersion('1.1');

        $postParameters = ['status' => $text];

        if (!empty($mediaResult['ids'])) {
            $postParameters['media_ids'] = implode(',', $mediaResult['ids']);
        }

        $postResult = $this->connection->post('statuses/update', $postParameters);

        return $this->buildResponse($postResult, function () use ($postResult) {
            return [
                'id' => $postResult->id
            ];
        });
    }

    protected function storePostWithApiV2(string $text, array $mediaResult): SocialProviderResponse
    {
        $this->connection->setApiVersion(2);

        $postParameters = ['text' => $text];

        if (!empty($mediaResult['ids'])) {
            $postParameters['media']['media_ids'] = $mediaResult['ids'];
        }

        $postResult = $this->connection->post('tweets', $postParameters, ['jsonPayload' => true]);

        return $this->buildResponse($postResult, function () use ($postResult) {
            return [
                'id' => $postResult->data->id
            ];
        });
    }

    public function uploadMedia(Collection $media): array
    {
        $this->connection->setApiVersion('1.1');

        $ids = [];
        $errors = [];

        foreach ($media as $item) {
            $chunkUpload = $item->isVideo() || $item->isImageGif();

            if (!$chunkUpload) {
                $result = $this->connection->upload('media/upload', [
                    'media' => $item->isLocalAdapter() ? $item->getFullPath() : $item->getUrl(),
                    'media_type' => $item->mime_type,
                    'media_category' => 'tweet_image',
                    'total_bytes' => $item->size,
                ]);
            }

            if ($chunkUpload) {
                /** @var string|array $mediaFilePath * */
                $mediaFilePath = $item->isLocalAdapter() ?
                    $item->getFullPath() :
                    $item->downloadToTemp();

                $result = $this->connection->upload('media/upload', [
                    'media' => $mediaFilePath['fullPath'] ?? $mediaFilePath,
                    'media_type' => $item->mime_type,
                    'media_category' => $item->isImageGif() ? 'tweet_gif' : 'tweet_video',
                    'total_bytes' => $item->size,
                ], ['chunkedUpload' => true]);

                if (isset($mediaFilePath['temporaryDirectory'])) {
                    $mediaFilePath['temporaryDirectory']->delete();
                }
            }

            if (!$result) {
                $errors[] = $result;
                continue;
            }

            // Check status of uploaded media
            if (isset($result->processing_info)) {
                $state = $result->processing_info->state;
                $sleepSeconds = $result->processing_info->check_after_secs;

                do {
                    sleep($sleepSeconds);

                    $mediaStatus = $this->connection->mediaStatus($result->media_id);

                    $state = $mediaStatus->processing_info->state;
                    $sleepSeconds = $mediaStatus->processing_info->check_after_secs ?? 1;

                } while (in_array($state, ['pending', 'in_progress']));

                if ($state === 'failed') {
                    $errors[] = "Failed to upload {$item['name']} file.";
                    continue;
                }
            }

            $ids[] = $result->media_id_string;
        }

        return [
            'ids' => $ids,
            'errors' => $errors
        ];
    }

    public function getAccountMetrics(): SocialProviderResponse
    {
        $response = $this->connection->get('users/me', ['user.fields' => 'public_metrics']);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'followers_count' => $response->data->public_metrics->followers_count,
                'following_count' => $response->data->public_metrics->following_count,
                'tweet_count' => $response->data->public_metrics->tweet_count,
                'listed_count' => $response->data->public_metrics->listed_count,
            ];
        });
    }

    public function getUserTweetTimeline(string $userId, string $paginationToken = ''): SocialProviderResponse
    {
        $params = [
            'tweet.fields' => 'public_metrics,created_at,in_reply_to_user_id',
            'start_time' => Carbon::now('UTC')->subMonths(3)->startOfDay()->toRfc3339String(),
            'exclude' => 'retweets,replies',
            'max_results' => 100
        ];

        if ($paginationToken) {
            $params['pagination_token'] = $paginationToken;
        }

        $response = $this->connection->get("users/$userId/tweets", $params);

        return $this->buildResponse($response, function () use ($response) {
            return [
                'data' => $response->data ?? [],
                'meta' => $response->meta ?? null,
            ];
        });
    }

    public function deletePost($id): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::OK, []);
    }
}
