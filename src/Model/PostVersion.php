<?php

namespace Inovector\Mixpost\Model;

use Illuminate\Database\Eloquent\Model;

class PostVersion extends Model
{
    public $table = 'mixpost_post_versions';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'is_original',
        'content'
    ];

    protected $casts = [
        'is_original' => 'boolean',
        'content' => 'array',
    ];
}
