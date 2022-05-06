<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

class SyncApiConfig
{
    /**
     * @api
     *
     * @return string
     */
    public function getDefaultRelativePathToOpenApiFile(): string
    {
        $pathFragments = [
            'config',
            'api',
            'openapi',
            'openapi.yml',
        ];

        return implode(DIRECTORY_SEPARATOR, $pathFragments);
    }
}
