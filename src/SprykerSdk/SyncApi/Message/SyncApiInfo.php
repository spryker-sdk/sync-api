<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Message;

use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments;

class SyncApiInfo
{
    /**
     * @param string $resource
     * @param string $moduleName
     * @param string $httpMethod
     * @param string $httpResponseCode
     *
     * @return string
     */
    public static function addedGlueResourceMethodResponse(string $resource, string $moduleName, string $httpMethod, string $httpResponseCode): string
    {
        return SyncApiMessageFormatter::format(sprintf('Added "%s" run for "[%s] %s %s" resource in "%s" module.', GlueResourceMethodResponseArguments::SPRYK_NAME, strtoupper($httpMethod), $resource, $httpResponseCode, $moduleName));
    }

    /**
     * @param string $transferName
     * @param string $moduleName
     *
     * @return string
     */
    public static function addedTransfer(string $transferName, string $moduleName): string
    {
        return SyncApiMessageFormatter::format(sprintf('Added "%s" run for "%s" transfer in "%s" module.', TransferArguments::SPRYK_NAME, $transferName, $moduleName));
    }

    /**
     * @return string
     */
    public static function openApiSchemaFileIsValid(): string
    {
        return SyncApiMessageFormatter::format('Open API file doesn\'t contain any errors.');
    }

    /**
     * @return string
     */
    public static function generatedCodeFromOpenApiSchema(): string
    {
        return SyncApiMessageFormatter::format('Successfully generated code to work with synchronous API.');
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function openApiFileCreated(string $fileName): string
    {
        return SyncApiMessageFormatter::format(sprintf('Successfully created "%s".', $fileName));
    }
}
