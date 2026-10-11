<?php

namespace App\Support;

/** Which kind of portal this is. See config/portal.php. */
class Portal
{
    public static function hosted(): bool
    {
        return config('portal.mode') === 'hosted';
    }

    public static function single(): bool
    {
        return ! self::hosted();
    }

    public static function demo(): bool
    {
        return (bool) config('portal.demo');
    }

    /** The largest photo upload in kilobytes, for the `max` rule: the setting, or less when PHP allows less. */
    public static function maxUploadKilobytes(): int
    {
        $limits = [config('portal.max_upload_mb') * 1024];

        foreach (['upload_max_filesize', 'post_max_size'] as $setting) {
            $bytes = ini_parse_quantity((string) ini_get($setting));
            if ($bytes > 0) {
                $limits[] = intdiv($bytes, 1024);
            }
        }

        return min($limits);
    }

    /** The largest photo upload for people, like "8MB". */
    public static function maxUploadLabel(): string
    {
        $megabytes = self::maxUploadKilobytes() / 1024;

        return (floor($megabytes) == $megabytes ? (int) $megabytes : number_format($megabytes, 1, ',', '')).'MB';
    }
}
