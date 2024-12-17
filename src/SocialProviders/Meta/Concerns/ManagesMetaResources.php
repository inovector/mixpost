<?php

namespace Inovector\Mixpost\SocialProviders\Meta\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Enums\SocialProviderResponseStatus;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Support\SocialProviderResponse;
use Inovector\Mixpost\Util;

trait ManagesMetaResources
{
    public function getUserAccount(): SocialProviderResponse
    {
        $response = Http::get("$this->apiUrl/$this->apiVersion/me", [
            'fields' => 'id,name',
            'access_token' => $this->getAccessToken()['access_token']
        ]);

        return $this->buildResponse($response, function () use ($response) {
            $data = $response->json();

            return [
                'id' => $data['id'],
                'name' => $data['name'],
                'username' => '',
                'image' => $this->apiUrl . '/' . $this->apiVersion . '/' . $data['id'] . '/picture?normal',
            ];
        });
    }

    public function getAccount(): SocialProviderResponse
    {
        return $this->getUserAccount();
    }

    public function publishFacebookPost(string $text, Collection $media, array $params, string $accessToken): SocialProviderResponse
    {
        $pageId = $this->values['provider_id'];
        $isVideo = $media->count() === 1 && $media->first()->isVideo();

        // Publish a post in page feed with attached media.
        // `attached_media` = only images support
        if (!$isVideo) {
            $uploadMedia = $this->uploadImages($media, $pageId, $accessToken);

            if ($uploadMedia instanceof SocialProviderResponse) {
                return $uploadMedia;
            }

            $postParams = [
                'message' => $text,
                'access_token' => $accessToken
            ];

            if (!empty($uploadMedia)) {
                $postParams['attached_media'] = $uploadMedia;
            }

            $response = Http::post("$this->apiUrl/$this->apiVersion/$pageId/feed", $postParams);

            return $this->buildResponse($response, function () use ($response) {
                return [
                    'id' => $response->json()['id']
                ];
            });
        }

        // Publish as a video post with description
        $thumbReadStream = $media->first()->readStream('thumb');

        $response = $this->uploadVideo(
            mediaItem: $media->first(),
            targetId: $pageId,
            accessToken: $accessToken,
            meta: [
                'description' => $text,
                'thumb' => $thumbReadStream['stream']
            ]);

        if (is_resource($thumbReadStream['stream'])) {
            fclose($thumbReadStream['stream']);
        }

        $thumbReadStream['temporaryDirectory']?->delete();

        return $response;
    }

    public function uploadImage(Media $mediaItem, string $targetId, string $accessToken): SocialProviderResponse
    {
        $readStream = $mediaItem->readStream();

        $response = Http::attach('source', $readStream['stream'])
            ->post("$this->apiUrl/$this->apiVersion/$targetId/photos", [
                'published' => false,
                'access_token' => $accessToken
            ]);

        Util::closeAndDeleteStreamResource($readStream);

        return $this->buildResponse($response);
    }

    public function uploadVideo(Media $mediaItem, string $targetId, string $accessToken, array $meta = []): SocialProviderResponse
    {
        // Start
        $session = $this->buildResponse(Http::post("$this->apiUrl/$this->apiVersion/$targetId/videos", [
            'upload_phase' => 'start',
            'file_size' => $mediaItem->size,
            'access_token' => $accessToken
        ]));

        if ($session->hasError()) {
            return $session;
        }

        // Upload chunk
        $uploadSessionId = $session->context()['upload_session_id'];
        $startOffset = $session->context()['start_offset'];
        $endOffset = $session->context()['end_offset'];

        $readStream = $mediaItem->readStream();

        do {
            $partialFile = stream_get_contents($readStream['stream'], ($endOffset - $startOffset), $startOffset);

            $chunkResponse = $this->buildResponse(Http::attach('video_file_chunk', $partialFile, $mediaItem->name, [
                'Content-Type' => $mediaItem->mime_type
            ])
                ->post("$this->apiUrl/$this->apiVersion/$targetId/videos", [
                    'upload_phase' => 'transfer',
                    'upload_session_id' => $uploadSessionId,
                    'start_offset' => $startOffset,
                    'access_token' => $accessToken
                ]));

            if ($chunkResponse->hasError()) {
                if (is_resource($readStream['stream'])) {
                    fclose($readStream['stream']);
                }

                $readStream['temporaryDirectory']?->delete();

                return $chunkResponse;
            }

            $startOffset = $chunkResponse->context()['start_offset'];
            $endOffset = $chunkResponse->context()['end_offset'];
        } while ($startOffset !== $endOffset);

        if (is_resource($readStream['stream'])) {
            fclose($readStream['stream']);
        }

        $readStream['temporaryDirectory']?->delete();

        // Finish
        $finish = $this->buildResponse(Http::asMultipart()->post("$this->apiUrl/$this->apiVersion/$targetId/videos", array_merge([
            'upload_phase' => 'finish',
            'upload_session_id' => $uploadSessionId,
            'access_token' => $accessToken
        ], $meta)));

        if ($finish->hasError()) {
            return $finish;
        }

        if (!$finish->context()['success']) {
            return new SocialProviderResponse(SocialProviderResponseStatus::ERROR, ['Error uploading video file.']);
        }

        return new SocialProviderResponse(SocialProviderResponseStatus::OK, ['id' => $session->context()['video_id']]);
    }

    public function uploadImages(Collection $media, string $targetId, string $accessToken): array|SocialProviderResponse
    {
        $ids = [];

        foreach ($media as $item) {
            if ($item->isImage()) {
                $uploadResult = $this->uploadImage($item, $targetId, $accessToken);

                if ($uploadResult->hasExceededRateLimit()) {
                    return $uploadResult;
                }

                if ($id = $uploadResult->id) {
                    $ids[] = ['media_fbid' => $id];
                }
            }
        }

        return $ids;
    }

    public function publishPost(string $text, Collection $media, array $params = []): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::NO_CONTENT, []);
    }

    public function deletePost($id): SocialProviderResponse
    {
        return $this->response(SocialProviderResponseStatus::OK, []);
    }
}
