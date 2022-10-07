<?php

namespace SprykerSdk\SyncApi\OpenApi\Merge\Strategy;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class ReplaceRecursiveMergeStrategy implements MergeStrategyInterface
{
    use FieldAccessorTrait;

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param string|null $fieldToMerge
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentTransfer
     */
    public function merge(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        string $fieldToMerge = null
    ): OpenApiDocumentTransfer {
        $targetData = $this->getField($targetOpenApiDocumentTransfer, $fieldToMerge);
        $sourceData = $this->getField($sourceOpenApiDocumentTransfer, $fieldToMerge);

        $this->setField(
            $targetOpenApiDocumentTransfer,
            $fieldToMerge,
            array_replace_recursive($targetData, $sourceData)
        );

        return $targetOpenApiDocumentTransfer;
    }
}
