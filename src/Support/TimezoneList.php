<?php

namespace Inovector\Mixpost\Support;

use DateTime;
use DateTimeZone;

class TimezoneList
{
    protected bool $splitGroup = false;

    protected bool $includeGeneral = true;

    protected bool $showOffset = true;

    protected string $offsetPrefix = 'GMT';

    protected array $generalTimezones = [
        'UTC',
    ];

    protected array $continents = [
        'Africa' => DateTimeZone::AFRICA,
        'America' => DateTimeZone::AMERICA,
        'Antarctica' => DateTimeZone::ANTARCTICA,
        'Arctic' => DateTimeZone::ARCTIC,
        'Asia' => DateTimeZone::ASIA,
        'Atlantic' => DateTimeZone::ATLANTIC,
        'Australia' => DateTimeZone::AUSTRALIA,
        'Europe' => DateTimeZone::EUROPE,
        'Indian' => DateTimeZone::INDIAN,
        'Pacific' => DateTimeZone::PACIFIC,
    ];

    public function splitGroup(bool $status = true): static
    {
        $this->splitGroup = $status;

        return $this;
    }

    public function includeGeneral(bool $status = true): static
    {
        $this->includeGeneral = $status;

        return $this;
    }

    public function showOffset(bool $status = true): static
    {
        $this->showOffset = $status;

        return $this;
    }

    public function list(): array|object
    {
        $list = [];

        // Do not split group
        if (!$this->splitGroup) {
            if ($this->includeGeneral) {
                foreach ($this->generalTimezones as $timezone) {
                    $list[$timezone] = $timezone;
                }
            }

            foreach ($this->continents as $continent => $mask) {
                $timezones = DateTimeZone::listIdentifiers($mask);

                foreach ($timezones as $timezone) {
                    $list[$timezone] = $this->formatTimezone($timezone);
                }
            }

            return $list;
        }

        // Split group
        if ($this->includeGeneral) {
            foreach ($this->generalTimezones as $timezone) {
                $list['General'][$timezone] = $timezone;
            }
        }

        foreach ($this->continents as $continent => $mask) {
            $timezones = DateTimeZone::listIdentifiers($mask);

            foreach ($timezones as $timezone) {
                $list[$continent][$timezone] = $this->formatTimezone($timezone, $continent);
            }
        }

        return $list;
    }

    protected function formatTimezone(string $timezone, null|string $cutOffContinent = null): string
    {
        $displayedTimezone = empty($cutOffContinent) ? $timezone : substr($timezone, strlen($cutOffContinent) + 1);
        $normalizedTimezone = $this->normalizeTimezone($displayedTimezone);

        if (!$this->showOffset) {
            return $normalizedTimezone;
        }

        $separator = $this->normalizeSeparator();

        return '(' . $this->offsetPrefix . $this->getOffset($timezone) . ')' . $separator . $normalizedTimezone;
    }

    protected function normalizeTimezone(string $timezone): string
    {
        $search = ['St_', '/', '_'];
        $replace = ['St. ', ' / ', ' '];

        return str_replace($search, $replace, $timezone);
    }

    protected function normalizeSeparator(): string
    {
        return ' ';
    }

    protected function getOffset(string $timezone): string
    {
        $time = new DateTime('', new DateTimeZone($timezone));

        return $time->format('P');
    }
}
