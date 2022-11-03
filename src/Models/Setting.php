<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $table = 'mixpost_settings';

    protected $fillable = [
        'name',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public $timestamps = false;
}
