<?php

namespace Inovector\Mixpost\Collection;

use Illuminate\Support\Arr;
use Inovector\Mixpost\Enums\ServiceGroup;

class ServiceCollection
{
    public function __construct(public readonly array $services)
    {
    }

    public function group(ServiceGroup|array $group = null): static
    {
        return new static(
            array_values(
                array_filter($this->services, function ($serviceClass) use ($group) {
                    return in_array($serviceClass::group(), Arr::wrap($group));
                })
            )
        );
    }

    public function getClasses(): array
    {
        return $this->services;
    }

    public function getNames(): array
    {
        return array_map(fn($service) => $service::name(), $this->services);
    }

    public function getCollection(): array
    {
        return array_map(function ($serviceClass) {
            return [
                'name' => $serviceClass::name(),
                'group' => $serviceClass::group(),
                'form' => $serviceClass::form(),
            ];
        }, $this->services);
    }

    public function __array(): array
    {
        return $this->services;
    }
}
