<?php

namespace Inovector\Mixpost\Concerns\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            $model->uuid = $model->uuid ?? (string)Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public static function findByUuid($uuid): ?static
    {
        return static::where('uuid', $uuid)->first();
    }

    public static function firstOrFailByUuid($uuid): static
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }

    public static function firstOrFailTrashedByUuid($uuid): static
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }
}
