<?php

namespace Inovector\Mixpost\Support;

use Inovector\Mixpost\Concerns\Makeable;
use JsonSerializable;

/**
 * @method static static make()
 */
class SocialProviderPostConfigs implements JsonSerializable
{
    use Makeable;

    private bool $simultaneousPosting = true;

    private array $minTextChar = ['default' => 0];

    private array $maxTextChar = ['default' => 500];

    private array $minPhotos = ['default' => 0];

    private array $maxPhotos = ['default' => 4];

    private array $minVideos = ['default' => 0];

    private array $maxVideos = ['default' => 1];

    private array $minGifs = ['default' => 0];

    private array $maxGifs = ['default' => 1];

    private array $allowMixingMediaTypes = ['default' => false];

    private array $mediaTextRequirementOperator = ['default' => 'or']; // or, and

    public function simultaneousPosting(bool $value): SocialProviderPostConfigs
    {
        $this->simultaneousPosting = $value;

        return $this;
    }

    public function minTextChar(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->minTextChar, $value, $type);

        return $this;
    }

    public function maxTextChar(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->maxTextChar, $value, $type);

        return $this;
    }

    public function minPhotos(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->minPhotos, $value, $type);

        return $this;
    }

    public function maxPhotos(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->maxPhotos, $value, $type);

        return $this;
    }

    public function minVideos(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->minVideos, $value, $type);

        return $this;
    }

    public function maxVideos(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->maxVideos, $value, $type);

        return $this;
    }

    public function minGifs(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->minGifs, $value, $type);

        return $this;
    }

    public function maxGifs(int $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->maxGifs, $value, $type);

        return $this;
    }

    public function allowMixingMediaTypes(?bool $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->allowMixingMediaTypes, $value, $type);

        return $this;
    }

    public function mediaTextRequirementOperator(?string $value = null, string $type = 'default'): SocialProviderPostConfigs
    {
        $this->setConfigArrayValue($this->mediaTextRequirementOperator, $value, $type);

        return $this;
    }

    private function setConfigArrayValue(array &$config, mixed $value, string $type): void
    {
        $config[$type] = $value !== null ? $value : $config['default'];
    }

    public function jsonSerialize(): array
    {
        return [
            'simultaneous_posting' => $this->simultaneousPosting,
            'text_char_limit' => [
                'min' => $this->minTextChar,
                'max' => $this->maxTextChar,
            ],
            'media_limit' => [
                'min' => [
                    'photos' => $this->minPhotos,
                    'videos' => $this->minVideos,
                    'gifs' => $this->minGifs,
                ],
                'max' => [
                    'photos' => $this->maxPhotos,
                    'videos' => $this->maxVideos,
                    'gifs' => $this->maxGifs,
                    'allow_mixing' => $this->allowMixingMediaTypes,
                ],
            ],
            'media_text_requirement_operator' => $this->mediaTextRequirementOperator,
        ];
    }
}
