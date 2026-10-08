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
}
