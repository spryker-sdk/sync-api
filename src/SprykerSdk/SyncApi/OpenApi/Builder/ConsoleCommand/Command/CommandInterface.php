<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command;

use cebe\openapi\spec\OpenApi;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;

interface CommandInterface
{
    /**
     * @param string $sprykMode
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function build(
        string $sprykMode,
        OpenApi $openApi,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer
    ): OpenApiResponseTransfer;
}
