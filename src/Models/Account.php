<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Inovector\Mixpost\Casts\AccountMediaCast;

class Account extends Model
{
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
        'access_token' => 'array',
    ];

    protected static function booted()
    {
        static::deleted(function ($account) {
            // Remove media
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
}
