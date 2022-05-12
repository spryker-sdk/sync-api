<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Messages;

class SyncApiMessages
{
    /**
     * @var string
     */
    public const VALIDATOR_ERROR_NO_PATHS_DEFINED = 'Couldn\'t find any path definition. You need at least one.';

    /**
     * @var string
     */
    public const VALIDATOR_ERROR_NO_COMPONENTS_DEFINED = 'Couldn\'t find any component definition. You need at least one.';

    /**
     * @var string
     */
    public const VALIDATOR_ERROR_INVALID_HTTP_METHOD_IN_PATH_PATTERN = 'Found invalid HTTP method "%s" in path "%s".';

    /**
     * @var string
     */
    public const VALIDATOR_MESSAGE_OPEN_API_SUCCESS = 'Open API file doesn\'t contain any errors.';

    /**
     * @var string
     */
    public const SUCCESS_MESSAGE_OPEN_API_FILE_CREATED_PATTERN = 'Successfully created "%s".';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_COULD_NOT_PARSE_OPEN_API_FILE_PATTERN = 'Couldn\'t not parse OpenApi schema file "%s".';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_OPEN_API_FILE_DOES_NOT_EXIST_PATTERN = 'Couldn\'t find OpenApi schema file "%s".';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_OPEN_API_FILE_ALREADY_EXISTS_PATTERN = 'Couldn\'t create "%s" as it already exists. You can manually update it.';

    /**
     * @var string
     */
    public const SUCCESS_MESSAGE_GENERATED_CODE_FROM_OPEN_API_SCHEMA = 'Successfully generated code to work with synchronous API.';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_COULD_NOT_GENERATE_FROM_OPEN_API_SCHEMA = 'Could not generate code from OpenAPI schema file. Schema file is not valid, please run validation before generating code.';

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function errorMessageOpenApiFileAlreadyExists(string $fileName): string
    {
        return sprintf(static::ERROR_MESSAGE_OPEN_API_FILE_ALREADY_EXISTS_PATTERN, $fileName);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function errorMessageCouldNotParseOpenApiFile(string $fileName): string
    {
        return sprintf(static::ERROR_MESSAGE_COULD_NOT_PARSE_OPEN_API_FILE_PATTERN, $fileName);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function errorMessageOpenApiFileDoesNotExist(string $fileName): string
    {
        return sprintf(static::ERROR_MESSAGE_OPEN_API_FILE_DOES_NOT_EXIST_PATTERN, $fileName);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function successMessageOpenApiFileCreated(string $fileName): string
    {
        return sprintf(static::SUCCESS_MESSAGE_OPEN_API_FILE_CREATED_PATTERN, $fileName);
    }

    /**
     * @param string $path
     * @param string $httpMethod
     *
     * @return string
     */
    public static function validationErrorInvalidHttpMethodInPath(string $path, string $httpMethod): string
    {
        return sprintf(static::VALIDATOR_ERROR_INVALID_HTTP_METHOD_IN_PATH_PATTERN, $path, $httpMethod);
    }
}
