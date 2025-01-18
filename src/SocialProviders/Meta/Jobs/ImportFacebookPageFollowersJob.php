<?php

namespace Inovector\Mixpost\SocialProviders\Meta\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Inovector\Mixpost\Concerns\Job\HasSocialProviderJobRateLimit;
use Inovector\Mixpost\Concerns\Job\SocialProviderException;
use Inovector\Mixpost\Concerns\Job\SocialProviderJobFail;
use Inovector\Mixpost\Concerns\UsesSocialProviderManager;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Audience;
use Inovector\Mixpost\SocialProviders\Meta\FacebookPageProvider;
use Inovector\Mixpost\Support\SocialProviderResponse;

class ImportFacebookPageFollowersJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use UsesSocialProviderManager;
    use HasSocialProviderJobRateLimit;
    use SocialProviderJobFail;
    use SocialProviderException;

    public $deleteWhenMissingModels = true;

    public Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function handle(): void
    {
        if ($this->account->isUnauthorized()) {
            return;
        }

        if (!$this->account->isServiceActive()) {
            return;
        }

        if ($retryAfter = $this->rateLimitExpiration()) {
            $this->release($retryAfter);

            return;
        }

        /**
         * @see FacebookPageProvider
         * @var SocialProviderResponse $response
         */
        $response = $this->connectProvider($this->account)->getPageAudience();

        if ($response->isUnauthorized()) {
            $this->account->setUnauthorized();
            $this->captureException($response);

            return;
        }

        if ($response->hasExceededRateLimit()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
            $this->release($response->retryAfter());

            return;
        }

        if ($response->rateLimitAboutToBeExceeded()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
        }

        if ($response->hasError()) {
            // TODO: Create a table for logs all import, collect jobs in background
            $this->captureException($response);

            return;
        }

        Audience::updateOrCreate([
            'account_id' => $this->account->id,
            'date' => Carbon::today('UTC')->toDateString()
        ], [
            'total' => $response->context()['followers_count']
        ]);
    }
}
