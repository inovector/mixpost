<?php

namespace Inovector\Mixpost\SocialProviders\Meta\Concerns;

trait ManagesFacebookOAuth
{
    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->scope,
            'response_type' => 'code',
        ];

        $url = 'https://www.facebook.com/' . $this->apiVersion . '/dialog/oauth';

        return $this->buildUrlFromBase($url, $params);
    }
}
