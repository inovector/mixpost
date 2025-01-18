<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Inovector\Mixpost\Events\AccountAdded;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Support\MediaUploader;

class UpdateOrCreateAccount
{
    public function __invoke(string $providerName, array $account, array $accessToken): void
    {
        $account = Account::updateOrCreate(
            [
                'provider' => $providerName,
                'provider_id' => $account['id']
            ],
            [
                'name' => $account['name'],
                'username' => $account['username'] ?? null,
                'media' => $this->media($account['image'], $providerName),
                'data' => $account['data'] ?? null,
                'authorized' => true,
                'access_token' => $accessToken,
            ]
        );

        if ($account->wasRecentlyCreated) {
            AccountAdded::dispatch($account);
        }
    }

    protected function media(string|null $imageUrl, string $providerName): array|null
    {
        if (!$imageUrl) {
            return null;
        }

        $info = pathinfo($imageUrl);
        $contents = file_get_contents($imageUrl);
        $file = '/tmp/' . Str::random(32);
        file_put_contents($file, $contents);

        $file = new UploadedFile($file, $info['basename']);
        $path = "mixpost/avatars/$providerName";

        $upload = MediaUploader::fromFile($file)->path($path)->upload();

        return [
            'disk' => $upload['disk'],
            'path' => $upload['path']
        ];
    }
}
