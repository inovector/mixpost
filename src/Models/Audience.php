<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{
    public $table = 'mixpost_audience';

    protected $fillable = [
        'account_id',
        'total',
        'date',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public $timestamps = false;

    public function scopeAccount($query, int $accountId)
    {
        $query->where('account_id', $accountId);
    }
}
