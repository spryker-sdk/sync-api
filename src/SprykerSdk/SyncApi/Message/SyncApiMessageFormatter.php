<?php

namespace SprykerSdk\SyncApi\Message;

/**
 * Provides a formatter function for messages.
 */
class SyncApiMessageFormatter
{
    /**
     * Colorize output in CLI on Linux machines.
     *
     * Info text will be in green, everything in double quotes will be yellow, and quotes will be removed.
     *
     * @param string $message
     *
     * @return string
     */
    public static function format(string $message): string
    {
        if (PHP_SAPI === 'cli' && strtolower(substr(PHP_OS, 0, 3)) !== 'win') {
            $message = "\033[32m" . preg_replace_callback('/"(.+?)"/', function (array $matches) {
                return sprintf("\033[0m\033[33m%s\033[0m\033[32m", $matches[1]);
            }, $message);
        }

        return $message;
    }
}
