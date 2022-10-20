<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;

interface ComponentsCleanerInterface
{
    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function cleanUnused(OpenApi $openApi): OpenApi;
}
