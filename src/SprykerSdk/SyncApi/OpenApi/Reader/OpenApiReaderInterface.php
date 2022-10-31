<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Reader;

use cebe\openapi\spec\OpenApi;

interface OpenApiReaderInterface
{
    /**
     * @param string $openApiDoc
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function readOpenApiFromJsonString(string $openApiDoc): OpenApi;

    /**
     * @param string $filePath
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function readOpenApiFromFile(string $filePath): OpenApi;
}
