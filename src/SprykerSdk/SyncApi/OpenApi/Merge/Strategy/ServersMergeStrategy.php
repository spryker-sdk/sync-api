<?php

namespace SprykerSdk\SyncApi\OpenApi\Merge\Strategy;

use ArrayObject;
use Generated\Shared\Transfer\OpenApiDocumentServerTransfer;
use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class ServersMergeStrategy implements MergeStrategyInterface
{
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
        $targetServerTransfers = $targetOpenApiDocumentTransfer->getServers();
        $sourceData = $sourceOpenApiDocumentTransfer->getServers();

        foreach ($sourceData as $sourceServerTransfer) {
            $targetServerTransfer = $this->findTargetServer($targetServerTransfers, $sourceServerTransfer);

            if ($targetServerTransfer !== null) {
                $targetServerTransfer->setDescription($sourceServerTransfer->getDescription());
            } else {
                $targetServerTransfers->append($sourceServerTransfer);
            }
        }

        return $targetOpenApiDocumentTransfer->setServers($targetServerTransfers);
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\OpenApiDocumentServerTransfer[] $targetServerTransfers
     * @param \Generated\Shared\Transfer\OpenApiDocumentServerTransfer $sourceServerTransfer
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentServerTransfer|null
     */
    protected function findTargetServer(
        ArrayObject $targetServerTransfers,
        OpenApiDocumentServerTransfer $sourceServerTransfer
    ): ?OpenApiDocumentServerTransfer {
        foreach ($targetServerTransfers as $targetServerTransfer) {
            if ($sourceServerTransfer->getUrl() === $targetServerTransfer->getUrl()) {
                return $targetServerTransfer;
            }
        }

        return null;
    }
}
