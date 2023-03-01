<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Exception;
use Inovector\Mixpost\Facades\Services;
use Symfony\Component\HttpFoundation\Response;

class CreateMastodonApp extends FormRequest
{
    public function rules(): array
    {
        return [
            'server' => ['required', 'string', 'max:255'],
        ];
    }

    public function handle(): void
    {
        $serviceName = "mastodon.{$this->input('server')}";

        if (Services::get($serviceName)) {
            return;
        }

        try {
            $credentials = Http::post("https:/{$this->input('server')}/api/v1/apps", [
                'client_name' => config('app.name'),
                'redirect_uris' => route('mixpost.callbackSocialProvider', ['provider' => 'mastodon']),
                'scopes' => 'read write',
                'website' => config('app.url')
            ])->json();

            (new UpdateOrCreateService())($serviceName, $credentials);
        } catch (Exception $exception) {
            $errors = ['server' => ['This Mastodon server is not responding or does not exist.']];

            throw new HttpResponseException(
                response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
