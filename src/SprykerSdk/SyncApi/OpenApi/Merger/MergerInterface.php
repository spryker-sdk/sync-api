<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;

interface MergerInterface
{
    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApiDocumentTransfer
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApiDocumentTransfer
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function merge(
        OpenApi $targetOpenApiDocumentTransfer,
        OpenApi $sourceOpenApiDocumentTransfer
    ): OpenApi;
}
