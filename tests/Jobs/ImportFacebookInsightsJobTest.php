<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Enums\FacebookInsightType;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\FacebookInsight;
use Inovector\Mixpost\Models\Service;
use Inovector\Mixpost\SocialProviders\Meta\Jobs\ImportFacebookInsightsJob;

beforeEach(function () {
    Service::factory()->create([
        'name' => 'facebook',
        'configuration' => ['client_id' => '1', 'client_secret' => 'secret', 'api_version' => 'v25.0'],
        'active' => true,
    ]);

    $this->account = Account::factory()->create([
        'provider' => 'facebook_page',
        'access_token' => ['page_access_token' => 'page-token'],
    ]);
});

it('only requests valid page insight metrics and imports them', function () {
    Http::fake([
        '*/insights*' => Http::response([
            'data' => [
                [
                    'name' => 'page_post_engagements',
                    'values' => [['value' => 7, 'end_time' => '2026-03-01T08:00:00+0000']],
                ],
            ],
        ]),
    ]);

    ImportFacebookInsightsJob::dispatchSync($this->account);

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(), '/insights')
            && $request['metric'] === 'page_post_engagements';
    });

    expect(FacebookInsight::where('account_id', $this->account->id)
        ->where('type', FacebookInsightType::PAGE_POST_ENGAGEMENTS)
        ->value('value'))->toBe(7);
});

it('does not throw when facebook rejects a metric', function () {
    Http::fake([
        '*/insights*' => Http::response([
            'error' => ['message' => '(#100) The value must be a valid insights metric', 'type' => 'OAuthException', 'code' => 100],
        ], 400),
    ]);

    ImportFacebookInsightsJob::dispatchSync($this->account);

    expect(FacebookInsight::where('account_id', $this->account->id)->count())->toBe(0);
});
