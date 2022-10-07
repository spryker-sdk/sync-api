<?php

namespace SprykerSdk\SyncApi\OpenApi\Merge\Strategy;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class ReplaceStrategy implements MergeStrategyInterface
{
    use FieldAccessorTrait;

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param string $fieldToMerge
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentTransfer
     */
    public function merge(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        string $fieldToMerge
    ): OpenApiDocumentTransfer {
        $this->setField(
            $targetOpenApiDocumentTransfer,
            $fieldToMerge,
            $this->getField($sourceOpenApiDocumentTransfer, $fieldToMerge)
        );

        return $targetOpenApiDocumentTransfer;
    }
}
