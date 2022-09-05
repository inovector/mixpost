<?php

namespace Inovector\Mixpost\Support;

use Inovector\Mixpost\Contracts\MediaConversion;

class MediaConversionData
{
    protected MediaConversion $conversion;

    public function __construct(MediaConversion $conversion)
    {
        $this->setConversion($conversion);
    }

    public static function conversion(MediaConversion $conversion): static
    {
        return new static($conversion);
    }

    public function get(): array
    {
        $reflection = new \ReflectionClass($this->conversion);

        return [
            'engine' => $this->conversion->getEngineName(),
            'path' => $this->conversion->getPath(),
            'disk' => $this->conversion->getToDisk(),
            'name' => $reflection->getProperty('name')->getValue($this->conversion),
        ];
    }

    private function setConversion(MediaConversion $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }
}
