<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{
    use HasFactory;

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
