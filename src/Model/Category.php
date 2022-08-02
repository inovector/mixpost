<?php

namespace Lao9s\Mixpost\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = 'mixpost_categories';

    protected $fillable = [
        'name',
        'hex_color'
    ];
}
