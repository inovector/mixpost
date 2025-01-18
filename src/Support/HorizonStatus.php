<?php

namespace Inovector\Mixpost\Support;

use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class HorizonStatus
{
    public function __construct(
        private readonly ?MasterSupervisorRepository $masterSupervisorRepository = null
    )
    {
    }

    public function get(): string
    {
        if (!$masters = $this->masterSupervisorRepository->all()) {
            return 'Inactive';
        }

        if (collect($masters)->contains(function ($master) {
            return $master->status === 'paused';
        })) {
            return 'Paused';
        }

        return 'Active';
    }
}
