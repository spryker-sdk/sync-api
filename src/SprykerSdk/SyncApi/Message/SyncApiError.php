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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
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
        return SyncApiMessageFormatter::format(
            sprintf(
                '%s: Couldn\'t find Open API schema file "%s".',
                static::SCHEMA_VALIDATION_ERROR_PREFIX,
                $path,
            ),
        );
    }
}
