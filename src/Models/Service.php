<?php

namespace Inovector\Mixpost\Models;

use Inovector\Mixpost\Casts\EncryptArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Inovector\Mixpost\Facades\ServiceManager;

class Service extends Model
{
    use HasFactory;

    public $table = 'mixpost_services';

    protected $fillable = [
        'name',
        'configuration',
        'active'
    ];

    protected $casts = [
        'configuration' => EncryptArrayObject::class,
        'active' => 'boolean'
    ];

    protected $hidden = [
        'configuration'
    ];

    public $timestamps = false;

    protected static function booted()
    {
        static::saved(function ($service) {
            ServiceManager::put(
                name: $service->name,
                configuration: $service->configuration->toArray(),
                active: $service->active
            );
        });

        static::deleted(function ($service) {
            ServiceManager::forget($service->name);
        });
    }
}
