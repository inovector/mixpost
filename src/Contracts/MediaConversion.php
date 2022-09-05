<?php

namespace Inovector\Mixpost\Contracts;

use Inovector\Mixpost\Support\MediaConversionData;

interface MediaConversion
{
    public function getEngineName(): string;

    public function getPath(): string;

    public function canPerform(): bool;

    public function handle(): MediaConversionData|null;
}
