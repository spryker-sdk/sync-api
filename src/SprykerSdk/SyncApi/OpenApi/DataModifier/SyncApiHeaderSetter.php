<?php

namespace SprykerSdk\SyncApi\OpenApi\DataModifier;

use Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer;

class SyncApiHeaderSetter extends AbstractDataModifierHandler
{
    /**
     * @var array
     */
    protected const SYNC_API_HEADER = ['syncapi' => '2.2.0'];

    /**
     * @param \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
     *
     * @return \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer
     */
    protected function modify(
        OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
    ): OpenApiDataModifierContainerTransfer {
        $targetData = $openApiDataModifierContainer->getTargetData();

        unset($targetData['openapi']);

        return $openApiDataModifierContainer->setTargetData(
            array_replace_recursive(
                static::SYNC_API_HEADER,
                $targetData
            ),
        );
    }
}
