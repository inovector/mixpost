<?php

namespace Inovector\Mixpost\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $table = 'mixpost_posts';

    protected $fillable = [
        'body',
        'status',
        'schedule_at',
        'delivered_at'
    ];

    protected $casts = [
        'schedule_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function versions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostVersion::class, 'post_id', 'id');
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'mixpost_category_post', 'post_id', 'category_id');
    }
}
