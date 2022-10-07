<?php

namespace SprykerSdk\SyncApi\OpenApi\Merge\Strategy;

use Generated\Shared\Transfer\OpenApiDocumentServerTransfer;
use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class ServersMergeStrategy implements MergeStrategyInterface
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param string $fieldToMerge
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentTransfer
     */
    public function merge(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
    ): OpenApiDocumentTransfer {
        $targetData = $targetOpenApiDocumentTransfer->getServers();
        $sourceData = $sourceOpenApiDocumentTransfer->getServers();

        foreach ($sourceData as $sourceServerTransfer) {
            $targetServerIndex = $this->getTargetServerIndex($targetData, $sourceServerTransfer);

            if ($targetServerIndex === null) {
                $targetData[] = $sourceServerTransfer;
                continue;
            }

            $targetData[$targetServerIndex] = $sourceServerTransfer;
        }

        return $targetOpenApiDocumentTransfer->setServers($targetData);
    }

    /**
     * @param array<\Generated\Shared\Transfer\OpenApiDocumentServerTransfer> $targetData
     * @param \Generated\Shared\Transfer\OpenApiDocumentServerTransfer $sourceServerTransfer
     *
     * @return int|null
     */
    protected function getTargetServerIndex(array $targetData, OpenApiDocumentServerTransfer $sourceServerTransfer): ?int
    {
        foreach ($targetData as $index => $targetServerTransfer) {
            if ($targetServerTransfer->getUrl() === $sourceServerTransfer->getUrl()) {
                return $index;
            }
        }

        return null;
    }
}
