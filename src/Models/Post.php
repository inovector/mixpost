<?php

namespace Inovector\Mixpost\Models;

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
        'published_at'
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'mixpost_post_accounts', 'post_id', 'account_id')
            ->withPivot('errors')
            ->orderByPivot('id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PostVersion::class, 'post_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'mixpost_tag_post', 'post_id', 'tag_id')
            ->orderByPivot('id');
    }

    public function canSchedule(): bool
    {
        return $this->status->name === PostStatus::DRAFT->name && $this->scheduled_at !== null;
    }

    public function isPublishing(): bool
    {
        return $this->status->name === PostStatus::PUBLISHING->name;
    }

    public function isPublished(): bool
    {
        return $this->status->name === PostStatus::PUBLISHED->name;
    }

    public function setScheduled()
    {
        $this->status = PostStatus::SCHEDULED->value;
        $this->save();
    }

    public function setPublishing()
    {
        $this->status = PostStatus::PUBLISHING->value;
        $this->save();
    }

    public function setPublished()
    {
        $this->status = PostStatus::PUBLISHED->value;
        $this->published_at = now();
        $this->save();
    }

    public function setError()
    {
        $this->status = PostStatus::ERROR->value;
        $this->save();
    }
}
