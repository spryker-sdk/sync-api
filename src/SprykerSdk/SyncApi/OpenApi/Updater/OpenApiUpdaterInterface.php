<?php

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use Generated\Shared\Transfer\UpdateOpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;

interface OpenApiUpdaterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UpdateOpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer;
}
