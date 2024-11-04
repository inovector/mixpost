<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Support\Facades\Http;
use Exception;

class CreateMastodonApp
{
    public function __invoke(string $serverName): array
    {
        $serviceName = "mastodon.$serverName";

        try {
            $configuration = Http::post("https:/$serverName/api/v1/apps", [
                'client_name' => config('app.name'),
                'redirect_uris' => route('mixpost.callbackSocialProvider', ['provider' => 'mastodon']),
                'scopes' => 'read write'
            ])->json();

            if (isset($configuration['error'])) {
                return [
                    'error' => $configuration['error']
                ];
            }

            (new UpdateOrCreateService())(
                name: $serviceName,
                configuration: $configuration,
                active: true
            );

            return $configuration;
        } catch (Exception $exception) {
            return [
                'error' => 'This Mastodon server is not responding or does not exist.'
            ];
        }
    }
}
