<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    public $table = 'mixpost_metrics';

    protected $fillable = [
        'account_id',
        'data',
        'date',
    ];

    protected $casts = [
        'data' => 'array',
        'date' => 'date'
    ];

    public $timestamps = false;

    public function scopeAccount($query, int $accountId)
    {
        $query->where('account_id', $accountId);
    }
}
