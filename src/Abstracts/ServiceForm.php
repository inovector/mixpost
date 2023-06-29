<?php

namespace Inovector\Mixpost\Abstracts;

use Inovector\Mixpost\Contracts\ServiceForm as ServiceFormRulesInterface;

abstract class ServiceForm implements ServiceFormRulesInterface
{
    /**
     * The attributes that should be considered as an additional configuration.
     */
    public static array $configs = [];
}
