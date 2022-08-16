<?php

namespace SprykerSdk\SyncApi\OpenApi\DataModifier;

use Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer;

interface DataModifierHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
     *
     * @return \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer
     */
    public function handle(
        OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
    ): OpenApiDataModifierContainerTransfer;
}
