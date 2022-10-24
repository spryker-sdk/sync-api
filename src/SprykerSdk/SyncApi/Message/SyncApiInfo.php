<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Message;

class SyncApiInfo
{
    /**
     * @return string
     */
    public static function openApiSchemaFileIsValid(): string
    {
        return static::format('Open API file doesn\'t contain any errors.');
    }

    /**
     * @return string
     */
    public static function generatedCodeFromOpenApiSchema(): string
    {
        return static::format('Successfully generated code to work with synchronous API.');
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function openApiFileCreated(string $fileName): string
    {
        return static::format(sprintf('Successfully created "%s".', $fileName));
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function openApiFileUpdated(string $fileName): string
    {
        return static::format(sprintf('Successfully updated "%s".', $fileName));
    }

    /**
     * Colorize output in CLI on Linux machines.
     *
     * Info text will be in green, everything in double quotes will be yellow, and quotes will be removed.
     *
     * @param string $message
     *
     * @return string
     */
    protected static function format(string $message): string
    {
        if (PHP_SAPI === PHP_SAPI && stripos(PHP_OS, 'WIN') === false) {
            $message = "\033[32m" . preg_replace_callback('/"(.+?)"/', function (array $matches) {
                return sprintf("\033[0m\033[33m%s\033[0m\033[32m", $matches[1]);
            }, $message);
        }

        return $message;
    }
}
