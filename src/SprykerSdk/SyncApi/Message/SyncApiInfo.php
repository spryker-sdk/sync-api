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
