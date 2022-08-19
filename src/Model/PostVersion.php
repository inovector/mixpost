<?php

namespace Lao9s\Mixpost\Model;

use Illuminate\Database\Eloquent\Model;

class PostVersion extends Model
{
    public $table = 'mixpost_post_versions';

    protected $fillable = [
        'account_id',
        'is_default',
        'content'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'content' => 'array',
    ];
}
