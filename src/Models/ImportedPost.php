<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedPost extends Model
{
    public $table = 'mixpost_imported_posts';

    protected $fillable = [
        'account_id',
        'provider_post_id',
        'content',
        'metrics',
        'created_at'
    ];

    protected $casts = [
        'content' => 'array',
        'metrics' => 'array',
        'created_at' => 'date'
    ];

    public $timestamps = false;
}
