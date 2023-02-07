<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Support\Facades\Http;

class CreateMastodonApp
{
    public function __invoke(string $server): array
    {
        $result = Http::post("https:/$server/api/v1/apps", [
            'client_name' => config('app.name'),
            'redirect_uris' => route('mixpost.callbackSocialProvider', ['provider' => 'mastodon']),
            'scopes' => 'read write',
            'website' => config('app.url')
        ]);

        return $result->json();
    }
}
