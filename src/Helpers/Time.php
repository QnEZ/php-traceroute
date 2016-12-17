<?php

namespace Qnez\TraceRoute\Helpers;

class Time
{
    const MICROSECOND = 1000000;

    public static function milliseconds($now, $time)
    {
        return round(($now - $time) / 1000, 2);
    }

    public static function currentTime()
    {
        return microtime(true) * self::MICROSECOND;
    }
}
