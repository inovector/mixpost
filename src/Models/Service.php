<?php

namespace Inovector\Mixpost\Models;

use Inovector\Mixpost\Casts\EncryptArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Inovector\Mixpost\Facades\Services as ServicesFacade;

class Service extends Model
{
    use HasFactory;

    public $table = 'mixpost_services';

    protected $fillable = [
        'name',
        'credentials'
    ];

    protected $casts = [
        'credentials' => EncryptArrayObject::class
    ];

    protected $hidden = [
        'credentials'
    ];

    public $timestamps = false;

    protected static function booted()
    {
        static::saved(function ($service) {
            ServicesFacade::put($service->name, $service->credentials->toArray());
        });

        static::deleted(function ($service) {
            ServicesFacade::forget($service->name);
        });
    }
}
