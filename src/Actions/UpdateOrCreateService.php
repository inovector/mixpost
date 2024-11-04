<?php

namespace Inovector\Mixpost\Actions;

use Inovector\Mixpost\Models\Service;

class UpdateOrCreateService
{
    public function __invoke(string $name, array $configuration, bool $active = false): Service
    {
        return Service::updateOrCreate(['name' => $name], [
            'configuration' => $configuration,
            'active' => $active
        ]);
    }
}
