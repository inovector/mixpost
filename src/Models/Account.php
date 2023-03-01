<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inovector\Mixpost\Casts\AccountMediaCast;

class Account extends Model
{
    use HasFactory;

    protected $table = 'mixpost_accounts';

    protected $fillable = [
        'name',
        'username',
        'media',
        'provider',
        'provider_id',
        'data',
        'access_token'
    ];

    protected $casts = [
        'media' => AccountMediaCast::class,
        'data' => 'array',
        'access_token' => AsEncryptedArrayObject::class
    ];

    protected $hidden = [
        'access_token'
    ];

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
            'name' => $this->name,
            'username' => $this->username,
            'data' => $this->data
        ];
    }

    public function providerOptions()
    {
        $rules = config('mixpost.social_provider_options');

        return Arr::get($rules, $this->provider);
    }
}
