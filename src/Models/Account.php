<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Inovector\Mixpost\Casts\AccountMediaCast;
use Inovector\Mixpost\Casts\EncryptArrayObject;
use Inovector\Mixpost\Concerns\Model\HasUuid;
use Inovector\Mixpost\Events\AccountUnauthorized;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\SocialProviders\Mastodon\MastodonProvider;
use Inovector\Mixpost\Support\SocialProviderPostConfigs;

class Account extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'mixpost_accounts';

    protected $fillable = [
        'name',
        'username',
        'media',
        'provider',
        'provider_id',
        'data',
        'authorized',
        'access_token'
    ];

    protected $casts = [
        'media' => AccountMediaCast::class,
        'data' => 'array',
        'authorized' => 'boolean',
        'access_token' => EncryptArrayObject::class
    ];

    protected $hidden = [
        'access_token'
    ];

    protected ?string $providerClass = null;

    protected static function booted()
    {
        static::updated(function ($account) {
            if ($account->wasChanged('media')) {
                Storage::disk($account->getOriginal('media')['disk'])->delete($account->getOriginal('media')['path']);
            }
        });

        static::deleted(function ($account) {
            if ($account->media) {
                Storage::disk($account->media['disk'])->delete($account->media['path']);
            }
        });
    }

    public function image(): ?string
    {
        if ($this->media) {
            return Storage::disk($this->media['disk'])->url($this->media['path']);
        }

        return null;
    }

    public function values(): array
    {
        return [
            'account_id' => $this->id,
            'provider_id' => $this->provider_id,
            'provider' => $this->provider,
            'name' => $this->name,
            'username' => $this->username,
            'data' => $this->data
        ];
    }

    public function getProviderClass()
    {
        if ($this->providerClass) {
            return $this->providerClass;
        }

        return $this->providerClass = SocialProviderManager::providers()[$this->provider] ?? null;
    }

    public function providerName(): string
    {
        if (!$provider = $this->getProviderClass()) {
            return $this->provider;
        }

        return $provider::name();
    }

    public function postConfigs(): array
    {
        if (!$provider = $this->getProviderClass()) {
            return SocialProviderPostConfigs::make()->jsonSerialize();
        }

        return $provider::postConfigs()->jsonSerialize();
    }

    public function isServiceActive(): bool
    {
        if (!$this->getProviderClass()) {
            return false;
        }

        if ($this->getProviderClass() === MastodonProvider::class) {
            return true;
        }

        return $this->getProviderClass()::service()::isActive();
    }

    public function isAuthorized(): bool
    {
        return $this->authorized;
    }

    public function isUnauthorized(): bool
    {
        return !$this->authorized;
    }

    public function setUnauthorized(bool $dispatchEvent = true): void
    {
        $this->authorized = false;
        $this->save();

        if ($dispatchEvent) {
            AccountUnauthorized::dispatch($this);
        }
    }

    public function setAuthorized(): void
    {
        $this->authorized = true;
        $this->save();
    }
}
