<?php

namespace Inovector\Mixpost;

use DateTimeInterface;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Inovector\Mixpost\Facades\Settings;

class Util
{
    public static function config(string $key, mixed $default = null)
    {
        return Config::get("mixpost.$key", $default);
    }

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

    public static function dateTimeFormat(Carbon $datetime, DateTimeZone|string|null $tz = null): string
    {
        $format = $datetime->year === now($tz)->year ? 'M j, ' . self::timeFormat() : 'M j, Y, ' . self::timeFormat();

        return $datetime->tz($tz ?: Settings::get('timezone'))->translatedFormat($format);
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

    public static function isPublicDomainUrl(string $url): bool
    {
        $parsedUrl = parse_url($url);

        if (empty($parsedUrl['host'])) {
            return false;
        }

        // Validate URL format
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        // Check if the host part is an IP address (both IPv4 and IPv6)
        if (filter_var($parsedUrl['host'], FILTER_VALIDATE_IP)) {
            return false;
        }

        if (in_array($parsedUrl['host'], ['localhost', '127.0.0.1', '::1'])) {
            return false;
        }

        return true;
    }

    public static function getDatabaseDriver(?string $connection = null): string
    {
        $key = is_null($connection) ? Config::get('database.default') : $connection;

        return strtolower(Config::get('database.connections.' . $key . '.driver'));
    }

    public static function isMysqlDatabase(?string $connection = null): bool
    {
        return self::getDatabaseDriver($connection) === 'mysql';
    }

    public static function closeAndDeleteStreamResource(array $stream): void
    {
        if (is_resource($stream['stream'])) {
            fclose($stream['stream']);
        }

        if (isset($stream['temporaryDirectory'])) {
            $stream['temporaryDirectory']->delete();
        }
    }

    public static function performTaskWithDelay(callable $task, int $initialDelay = 15, int $maxDelay = 60, int $maxAttempts = 10)
    {
        $delay = $initialDelay;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $result = $task();

            if ($result !== null) {
                return $result;
            }

            sleep($delay);

            // Increase delay for the next iteration, maxing out at maxDelay
            $delay = min($delay * 2, $maxDelay);
            // Add a random jitter to the delay
            $delay += rand(-(int)($delay * 0.1), (int)($delay * 0.1));

            $attempt++;
        }

        return null;
    }

    public static function isFFmpegInstalled(): bool
    {
        $ffmpegPath = Util::config('ffmpeg_path');
        $ffprobePath = Util::config('ffprobe_path');

        return file_exists($ffmpegPath) &&
            file_exists($ffprobePath) &&
            str_ends_with($ffmpegPath, 'ffmpeg') &&
            str_ends_with($ffprobePath, 'ffprobe');
    }
}
