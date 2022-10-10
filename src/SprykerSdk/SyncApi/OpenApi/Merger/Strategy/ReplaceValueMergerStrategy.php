<?php

namespace SprykerSdk\SyncApi\OpenApi\Merger\Strategy;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class ReplaceValueMergerStrategy implements MergerStrategyInterface
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
        $this->setField(
            $targetOpenApiDocumentTransfer,
            $fieldToMerge,
            $this->getField($sourceOpenApiDocumentTransfer, $fieldToMerge)
        );

        return $targetOpenApiDocumentTransfer;
    }
}
