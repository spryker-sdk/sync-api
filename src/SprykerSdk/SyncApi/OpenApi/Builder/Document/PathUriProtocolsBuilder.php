<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer;

class PathUriProtocolsBuilder implements PathUriProtocolsBuilderInterface
{
    /**
     * @param array $pathUriProtocolArray
     *
     * @return array<\Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer>
     */
    public function build(array $pathUriProtocolArray): array
    {
        $pathUriProtocolTransfers = [];

        foreach ($pathUriProtocolArray as $protocol => $contents) {
            $pathUriProtocolTransfers[] = (new OpenApiDocumentPathUriProtocolTransfer())
                ->setProtocol($protocol)
                ->setContents($contents);
        }

        return $pathUriProtocolTransfers;
    }
}
