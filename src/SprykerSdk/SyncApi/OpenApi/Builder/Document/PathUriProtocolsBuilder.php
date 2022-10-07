<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer;

class PathUriProtocolsBuilder implements PathUriProtocolsBuilderInterface
{
    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\RefsFinderInterface
     */
    protected $refsFinder;

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\RefsFinderInterface $refsFinder
     */
    public function __construct(RefsFinderInterface $refsFinder)
    {
        $this->refsFinder = $refsFinder;
    }

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
                ->setContents($contents)
                ->setRefs($this->refsFinder->findRefs($contents));
        }

        return $pathUriProtocolTransfers;
    }
}
