<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use Transfer\OpenApiResponseTransfer;
use Transfer\UpdateOpenApiRequestTransfer;

interface OpenApiUpdaterInterface
{
    /**
     * @param \Transfer\UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): OpenApiResponseTransfer;
}
