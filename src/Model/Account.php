<?php

namespace Lao9s\Mixpost\Model;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'mixpost_accounts';

    protected $fillable = [
        'name',
        'image',
        'provider',
        'provider_id',
        'credentials'
    ];

    protected $casts = [
        'credentials' => 'array'
    ];
}
