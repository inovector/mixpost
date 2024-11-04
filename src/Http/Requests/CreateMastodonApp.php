<?php

namespace Inovector\Mixpost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Inovector\Mixpost\Facades\ServiceManager;
use Symfony\Component\HttpFoundation\Response;
use Inovector\Mixpost\Actions\CreateMastodonApp as CreateMastodonAppAction;

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

        if (ServiceManager::get($serviceName, 'configuration')) {
            return;
        }

        $result = (new CreateMastodonAppAction())($this->input('server'));

        if (isset($result['error'])) {
            $errors = ['server' => [$result['error']]];

            throw new HttpResponseException(
                response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
