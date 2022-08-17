<?php

namespace Lao9s\Mixpost\Model;

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
        'credentials',
        'user_id'
    ];

    protected $casts = [
        'credentials' => 'array'
    ];
}
