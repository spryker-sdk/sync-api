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
        $targetFieldObject = $this->getField($targetOpenApiDocumentTransfer, $fieldToMerge);

        $targetData = $targetFieldObject->getContents();
        $sourceData = $this->getField($sourceOpenApiDocumentTransfer, $fieldToMerge)->getContents();

        $targetData = array_replace_recursive($targetData, $sourceData);

        $targetFieldObject->setContents($targetData);

        $this->setField(
            $targetOpenApiDocumentTransfer,
            $fieldToMerge,
            $targetFieldObject
        );

        return $targetOpenApiDocumentTransfer;
    }
}
