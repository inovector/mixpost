<?php

namespace Inovector\Mixpost\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inovector\Mixpost\Enums\PostStatus;

class Post extends Model
{
    public $table = 'mixpost_posts';

    protected $fillable = [
        'status',
        'scheduled_at',
        'delivered_at'
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'scheduled_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'mixpost_post_accounts', 'post_id', 'account_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PostVersion::class, 'post_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'mixpost_tag_post', 'post_id', 'tag_id');
    }
}
