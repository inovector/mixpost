<?php

namespace Inovector\Mixpost\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inovector\Mixpost\Enums\PostScheduleStatus;
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
        'schedule_status' => PostScheduleStatus::class,
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

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', PostStatus::FAILED->value);
    }

    public function canSchedule(): bool
    {
        // TODO: check if original content is not empty
        return $this->scheduled_at && !$this->scheduled_at->isPast() && $this->accounts()->exists();
    }

    public function isScheduled(): bool
    {
        return $this->status->name === PostStatus::SCHEDULED->name;
    }

    public function isPublished(): bool
    {
        return $this->status->name === PostStatus::PUBLISHED->name;
    }

    public function isFailed(): bool
    {
        return $this->status->name === PostStatus::FAILED->name;
    }

    public function isInHistory(): bool
    {
        return $this->isPublished() || $this->isFailed();
    }

    public function isScheduleProcessing(): bool
    {
        return $this->schedule_status->name === PostScheduleStatus::PROCESSING->name;
    }

    public function setScheduled(Carbon|null $date = null)
    {
        $this->status = PostStatus::SCHEDULED->value;

        if ($date) {
            $this->scheduled_at = $date;
        }

        $this->save();
    }

    public function setScheduleProcessing()
    {
        $this->schedule_status = PostScheduleStatus::PROCESSING;
        $this->save();
    }

    public function setPublished()
    {
        $this->status = PostStatus::PUBLISHED->value;
        $this->published_at = now();
        $this->schedule_status = PostScheduleStatus::PROCESSED;
        $this->save();
    }

    public function setFailed()
    {
        $this->status = PostStatus::FAILED->value;
        $this->schedule_status = PostScheduleStatus::PROCESSED;
        $this->save();
    }
}
