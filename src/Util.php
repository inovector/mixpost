<?php

namespace Inovector\Mixpost;

use DateTimeInterface;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inovector\Mixpost\Facades\Settings;

class Util
{
    public static function isMixpostRequest(Request $request): bool
    {
        $path = 'mixpost';

        return $request->is($path) ||
            $request->is("$path/*");
    }

    public static function convertTimeToUTC(string|DateTimeInterface|null $time = null, DateTimeZone|string|null $tz = null): Carbon
    {
        return Carbon::parse($time, $tz ?: Settings::get('timezone'))->utc();
    }

    public static function timeFormat(): string
    {
        return Settings::get('time_format') == 24 ? 'H:i' : 'h:ia';
    }

    public static function removeHtmlTags($string): string
    {
        if (!$string) {
            return '';
        }

        $text = trim(strip_tags($string));

        return html_entity_decode($text);
    }
}
