<?php

namespace Inovector\Mixpost\Exceptions;

use Exception;

class ServiceNotRegistered extends Exception
{
    public function __construct(string $service)
    {
        parent::__construct("Service `$service` is not registered");
    }
}
