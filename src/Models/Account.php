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
        'credentials'
    ];

    protected $casts = [
        'credentials' => 'array'
    ];
}
