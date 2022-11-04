<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $table = 'mixpost_tags';

    protected $fillable = [
        'name',
        'hex_color'
    ];
}
