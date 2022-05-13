<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Message;

class SyncApiError
{
    /**
     * @return string
     */
    public static function couldNotGenerateCodeFromOpenApi(): string
    {
        return static::format('Could not generate code from Open API schema file. Schema file is not valid, please run validation before generating code.');
    }

    /**
     * @return string
     */
    public static function openApiDoesNotDefineAnyPath(): string
    {
        return static::format('Couldn\'t find any path definition. You need at least one.');
    }

    /**
     * @return string
     */
    public static function openApiDoesNotDefineAnyComponents(): string
    {
        return static::format('Couldn\'t find any component definition. You need at least one.');
    }

    /**
     * @param string $httpMethod
     * @param string $path
     *
     * @return string
     */
    public static function openApiContainsInvalidHttpMethodForPath(string $httpMethod, string $path): string
    {
        return static::format(sprintf('Found invalid HTTP method "%s" in path "%s".', $httpMethod, $path));
    }

    /**
     * @param string $resource
     *
     * @return string
     */
    public static function canNotHandleResourcesWithPlaceholder(string $resource): string
    {
        return static::format(sprintf('Can\'t handle resources with placeholder at the moment. Resource "%s" can\'t be used to auto generate code.', $resource));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function canNotExtractAControllerNameForPath(string $path): string
    {
        return static::format(sprintf('Can\'t extract a controller name from path "%s".', $path));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function canNotExtractAModuleNameForPath(string $path): string
    {
        return static::format(sprintf('Can\'t extract a module name from path "%s".', $path));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function openApiFileAlreadyExists(string $path): string
    {
        return static::format(sprintf('Couldn\'t create "%s" as it already exists. You can manually update it.', $path));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function couldNotParseOpenApi(string $path): string
    {
        return static::format(sprintf('Couldn\'t not parse Open API schema file "%s".', $path));
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function couldNotFinOpenApi(string $path): string
    {
        return static::format(sprintf('Couldn\'t find Open API schema file "%s".', $path));
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
