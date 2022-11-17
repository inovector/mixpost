<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'mixpost_accounts';

    protected $fillable = [
        'name',
        'username',
        'image',
        'provider',
        'provider_id',
        'data',
        'access_token'
    ];

    protected $casts = [
        'data' => 'array',
        'access_token' => 'array',
    ];
}
