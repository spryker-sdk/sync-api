<?php

namespace SprykerSdk\SyncApi\OpenApi\DataModifier;

use Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer;

class DataSimpleRecursiveReplacer extends AbstractDataModifierHandler
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
     *
     * @return \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer
     */
    protected function modify(
        OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
    ): OpenApiDataModifierContainerTransfer {
        return $openApiDataModifierContainer->setTargetData(
            array_replace_recursive(
                $openApiDataModifierContainer->getTargetData(),
                $openApiDataModifierContainer->getModifyData(),
            )
        );
    }
}
