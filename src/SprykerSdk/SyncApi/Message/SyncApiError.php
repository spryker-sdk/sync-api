<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Message;

class SyncApiError
{
    /**
     * @var string
     */
    protected const SCHEMA_GENERATION_ERROR_PREFIX = 'SyncAPI schema generation error';

    /**
     * @var string
     */
    protected const SCHEMA_VALIDATION_ERROR_PREFIX = 'SyncAPI schema validation error';

    /**
     * @var string
     */
    protected const CODE_GENERATION_ERROR_PREFIX = 'SyncAPI code generation error';

    /**
     * @param string $path
     *
     * @return string
     */
    public static function couldNotGenerateCodeFromOpenApi(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Could not generate code from Open API schema file. Schema file "%s" is not valid, please run validation before generating code.',
                static::CODE_GENERATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function openApiDoesNotDefineAnyPath(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Couldn\'t find any path definition in your "%s" schema file. Please update the schema file.',
                static::SCHEMA_VALIDATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function openApiDoesNotDefineAnyComponents(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Couldn\'t find any component definition in your "%s" schema file. Please update the schema file.',
                static::SCHEMA_VALIDATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $httpMethod
     * @param string $path
     *
     * @return string
     */
    public static function openApiContainsInvalidHttpMethodForPath(string $httpMethod, string $path): string
    {
        return static::format(
            sprintf(
                '%s: Found invalid HTTP method "%s" in your "%s" schema file.',
                static::SCHEMA_VALIDATION_ERROR_PREFIX,
                $httpMethod,
                $path,
            ),
        );
    }

    /**
     * @param string $resource
     * @param string $path
     *
     * @return string
     */
    public static function canNotHandleResourcesWithPlaceholder(string $resource, string $path): string
    {
        return static::format(
            sprintf(
                '%s: Can\'t handle resources with placeholder at the moment. Resource "%s" from your "%s" schema file can\'t be used to auto generate code.',
                static::CODE_GENERATION_ERROR_PREFIX,
                $resource,
                $path,
            ),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function canNotExtractAControllerNameForPath(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Can\'t extract a controller name from path "%s".',
                static::CODE_GENERATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function canNotExtractAModuleNameForPath(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Can\'t extract a module name from path "%s".',
                static::CODE_GENERATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function openApiFileAlreadyExists(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Couldn\'t create "%s" as it already exists. You can manually update it.',
                static::SCHEMA_GENERATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function couldNotParseOpenApi(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Couldn\'t not parse Open API schema file "%s".',
                static::SCHEMA_VALIDATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function couldNotFindOpenApi(string $path): string
    {
        return static::format(
            sprintf(
                '%s: Couldn\'t find Open API schema file "%s".',
                static::SCHEMA_VALIDATION_ERROR_PREFIX,
                $path,
            ),
        );
    }

    /**
     * @param string $errorMessage
     *
     * @return string
     */
    public static function openApiDataIsInvalid(string $errorMessage): string
    {
        return static::format(
            sprintf(
                'Provided Open API data is invalid. Error: %s',
                $errorMessage,
            ),
        );
    }

    /**
     * @param string $errorMessage
     *
     * @return string
     */
    public static function couldNotUpdateOpenApiFile(string $errorMessage): string
    {
        return static::format(
            sprintf(
                'Update Open API failed with error: "%s"',
                $errorMessage,
            ),
        );
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
